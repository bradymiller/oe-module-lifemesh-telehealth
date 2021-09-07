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
namespace OpenEMR\Modules\LifeMesh;


use OpenEMR\Menu\MenuEvent;
use OpenEMR\Events\Appointments\AppointmentEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

require_once "controller/AppointmentSubscriber.php";

/**
 * @var EventDispatcherInterface $eventDispatcher
 * register subscriber to the appointment event
 */

$subscriber = new AppointmentSubscriber();
$eventDispatcher->addSubscriber($subscriber);


function oe_module_lifemesh_telehealth_add_session_button(Event $event)
{
    ?>
    <button type="button" class="btn btn-primary"><?php echo xlt('Start Session)'); ?></button>
<?php
}

$eventDispatcher->addListener(AppointmentEvent::ACTION_RENDER_SESSION, 'oe_module_lifemesh_telehealth_add_session_button',0);
