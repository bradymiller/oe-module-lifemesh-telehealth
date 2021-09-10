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

require_once dirname(__FILE__) . "/controller/AppointmentSubscriber.php";
require_once dirname(__FILE__, 5) . "/library/appointments.inc.php";

use OpenEMR\Events\Appointments\AppointmentAddEvent;
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

function oe_module_lifemesh_telehealth_cancel_javascript(Event $event)
{
    $retrieveid = new AppointmentAddEvent();

?>
function cancel_telehealth() {
let title = '<?php echo xlt('Cancel Telehealth Appt'); ?>';
let eid = '<?php echo $_GET['eid'] ?>';
dlgopen('../../modules/custom_modules/oe-module-lifemesh-telehealth/cancel_telehealth_session.php?eid='+eid, '', 650, 300, '', title);
}
<?php
}
function oe_module_lifemesh_telehealth_cancel_session(Event $event)
{
?>
    <span style="padding-right: 150px"><button class="btn btn-primary gray-background white padding" onclick="cancel_telehealth()">Cancel Telehealth</button></span>
<?php
}

function oe_module_lifemesh_telehealth_add_session_button(Event $event)
{
?>
   <span style="padding-left: 150px"><button type="button" class="btn btn-primary gray-background white">Start Session</button></span>

<?php
}


$eventDispatcher->addListener(AppointmentAddEvent::ACTION_RENDER_SESSION_BUTTON, 'oe_module_lifemesh_telehealth_add_session_button');
$eventDispatcher->addListener(AppointmentAddEvent::ACTION_RENDER_CANCEL_BUTTON, 'oe_module_lifemesh_telehealth_cancel_session');
$eventDispatcher->addListener(AppointmentAddEvent::ACTION_RENDER_CANCEL_JAVASCRIPT, 'oe_module_lifemesh_telehealth_cancel_javascript');

if (!empty(ismoduleactive())) {
    $telehealthDispatcher = $GLOBALS['kernel']->getEventDispatcher();
    //$telehealthDispatcher->dispatch(AppointmentTelehealthEvent::ACTION_RENDER_TELEHEALTH, new GenericEvent());
}
