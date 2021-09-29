<?php

/**
 * Bootstrap custom Telehealth module
 * @package       OpenEMR
 * @link          https://www.open-emr.org
 *
 * @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright Copyright (c) 2021 Sherwin Gaddis <sherwingaddis@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

require_once dirname(__FILE__) . "/controller/Container.php";
require_once dirname(__FILE__) . "/controller/AppointmentSubscriber.php";

use OpenEMR\Events\Appointments\AppointmentRenderEvent;
use OpenEMR\Modules\LifeMesh\Container;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use OpenEMR\Modules\LifeMesh\AppointmentSubscriber;




/**
 * @var EventDispatcherInterface $eventDispatcher
 * register subscriber to the appointment event
 */

$subscriber = new AppointmentSubscriber();
$eventDispatcher->addSubscriber($subscriber);

function oe_module_lifemesh_telehealth_render_javascript(AppointmentRenderEvent $event)
{
    $appt = $event->getAppt();

    if ((!empty($appt['pc_title'])) && (stristr($appt['pc_title'], 'telehealth'))) {
        $providersession =  ((new Container())->getDatabase())->getStoredSession($appt['pc_eid']);
        if (!empty($providersession)) {
            $code = $providersession['provider_code'];
            $uri = $providersession['provider_uri'];
        } else {
            error_log('No Lifemesh telehealth session found for a calendar event');
            ?>
            alert(<?php echo xlj('No Lifemesh telehealth session found for this calendar event'); ?>);
            <?php
            return;
        }

        ?>
        function cancel_telehealth() {
            if (confirm(<?php echo xlj('Are you sure you want to cancel the Telehealth session?'); ?>)) {
                document.getElementById("lifehealth-start").style.display = "none";
                document.getElementById("lifehealth-cancel").style.display = "none";
                let title = <?php echo xlj('Cancel Telehealth Appt'); ?>;
                let eid = <?php echo js_escape($appt['pc_eid']); ?>;
                dlgopen('../../modules/custom_modules/oe-module-lifemesh-telehealth/cancel_telehealth_session.php?eid=' + encodeURIComponent(eid), '', 650, 300, '', title);
            }
        }

        function startSession() {
            window.open(<?php echo js_escape($uri); ?>, '_blank', 'location=yes');
        }
        <?php
    }
}

function oe_module_lifemesh_telehealth_render_below_patient(AppointmentRenderEvent $event)
{
    // collect appointment information
    $appt = $event->getAppt();

    // check for existence and status of lifemesh account
    $container = new Container();
    $credentials =  $container->getDatabase()->getCredentials();
    $isCredentials = (!empty($credentials) && !empty($credentials[0]) && !empty($credentials[1]));
    if ($isCredentials) {
        $app = $container->getAppDispatch();
        $subscriberStatus = $app->apiRequest($credentials[1], $credentials[0], 'accountCheck');
        if (!$subscriberStatus) {
            $outputStatus = "Not working. " . $app->getStatusMessage();
            $statusBackground = "bg-danger";
            $statusIcon = "fa-exclamation-triangle";
        } else {
            $outputStatus = "OK";
            $statusBackground = "bg-success";
            $statusIcon = "fa-check-square";
        }
    } else {
        $outputStatus = "Not functional. A subscriber is not configured in the Lifemesh Telehealth module.";
        $statusBackground = "bg-danger";
        $statusIcon = "fa-exclamation-triangle";
    }

    ?>
    <div>
        <style>
            .gray-background { background-color: darkgray; }
            .white {color: #ffffff; }
        </style>
        <div class="d-inline-block ml-2 mt-2">
            <div class="d-inline-block <?php echo attr($statusBackground); ?>" data-toggle="tooltip" data-placement="right" title="Lifemesh Telehealth Module status: <?php echo attr($outputStatus); ?>">
                <img src="<?php echo $GLOBALS['webroot']; ?>/interface/modules/custom_modules/oe-module-lifemesh-telehealth/account/images/lifemesh-white-wordmark-transp-271x70-1.png" style="width:135px; height:35px;">
                <i class="mr-1 fa <?php echo attr($statusIcon); ?>" aria-hidden="true"></i>
            </div>
    <?php

    if ($isCredentials && $subscriberStatus && (!empty($appt['pc_title'])) && (stristr($appt['pc_title'], 'telehealth'))) {
        $providersession =  ((new Container())->getDatabase())->getStoredSession($appt['pc_eid']);
        if (!empty($providersession)) {
            if (empty($providersession['cancelled'])) {
            ?>
                <button type="button" class="ml-4 btn btn-primary gray-background white" id="lifehealth-start" onclick="startSession()"><?php echo xlt("Start Session"); ?></button>
                <button type="button" class="ml-2 btn btn-primary gray-background white" id="lifehealth-cancel" onclick="cancel_telehealth()"><?php echo xlt("Cancel Telehealth"); ?></button>
            <?php
            } else {
            ?>
                <span class="text-left ml-4"><?php echo xlt("This Telehealth session has been cancelled."); ?></span>
            <?php
            }
        }
    }

    ?>
        </div>
    </div>
    <?php
}

$eventDispatcher->addListener(AppointmentRenderEvent::RENDER_JAVASCRIPT, 'oe_module_lifemesh_telehealth_render_javascript');
$eventDispatcher->addListener(AppointmentRenderEvent::RENDER_BELOW_PATIENT, 'oe_module_lifemesh_telehealth_render_below_patient');
