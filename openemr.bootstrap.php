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
            let title = <?php echo xlj('Cancel Telehealth Appt'); ?>;
            let eid = <?php echo js_escape($appt['pc_eid']); ?>;
            dlgopen('../../modules/custom_modules/oe-module-lifemesh-telehealth/cancel_telehealth_session.php?eid=' + encodeURIComponent(eid), '', 650, 300, '', title);
        }

        function startSession() {
            window.open(<?php echo js_escape($uri); ?>, '_blank', 'location=yes');
        }
        <?php
    }
}

function oe_module_lifemesh_telehealth_render_below_patient(AppointmentRenderEvent $event)
{
    $appt = $event->getAppt();

    if ((!empty($appt['pc_title'])) && (stristr($appt['pc_title'], 'telehealth'))) {
        $providersession =  ((new Container())->getDatabase())->getStoredSession($appt['pc_eid']);
        if (!empty($providersession)) {
        ?>
            <div>
                <style>
                    .gray-background { background-color: darkgray; }
                    .white {color: #ffffff; }
                </style>
                <span style="padding-right: 150px"><button type="button" class="btn btn-primary gray-background white padding" onclick="cancel_telehealth()"><?php echo xlt("Cancel Telehealth"); ?></button></span>
                <span style="padding-left: 150px"><button type="button" class="btn btn-primary gray-background white" onclick="startSession()"><?php echo xlt("Start Session"); ?></button></span>
            </div>
        <?php
        }
    }
}

$eventDispatcher->addListener(AppointmentRenderEvent::RENDER_JAVASCRIPT, 'oe_module_lifemesh_telehealth_render_javascript');
$eventDispatcher->addListener(AppointmentRenderEvent::RENDER_BELOW_PATIENT, 'oe_module_lifemesh_telehealth_render_below_patient');
