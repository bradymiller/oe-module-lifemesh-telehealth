<?php
/*
 *
 * @package      OpenEMR
 * @link               https://www.open-emr.org
 *
 * @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright Copyright (c) 2021 Sherwin Gaddis <sherwingaddis@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

namespace OpenEMR\Modules\LifeMesh;

use OpenEMR\Events\Appointments\AppoinmentSetEvent;
use OpenEMR\Services\PatientService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AppointmentSubscriber implements EventSubscriberInterface
{
    public $createSession;
    public $patientfirstname;
    public $patientlastname;
    public $eventdate;

    public static function getSubscribedEvents() : array
    {
        return [
          AppoinmentSetEvent::EVENT_HANDLE  => 'isEventTelehealth'
        ];
    }

    public function isEventTelehealth(AppoinmentSetEvent $event)
    {
        $appointmentdata = $event->givenAppointmentData();
        $event_id = $event->eid;
        if (stristr($appointmentdata['form_title'], 'telehealth')) {
            $pid = $appointmentdata['form_pid'];
            $details = $this->getPatientDetails($pid);
            //$this->patientfirstname = $details['fname'];
            //$this->patientlastname = $details['lname'];
            //$this->eventdate = $appointmentdata['form_date'];
            //$creatsession = new AppDispatch();
            //$creatsession->apiRequestSession('', '', 'createsession');
            file_put_contents("/var/www/html/errors/event_eid.txt", $event_id);
        }
    }

    public function getPatientDetails($pid)
    {
        $sql = "SELECT email, phone_cell FROM patient_data";
    }




}
