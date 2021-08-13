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

use DateTimeZone;
use OpenEMR\Events\Appointments\AppoinmentSetEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

require_once "Container.php";

class AppointmentSubscriber implements EventSubscriberInterface
{
    public $callid;
    public $createSession;
    public $countrycode;
    public $eventdatetimeutc;
    public $eventdatetimelocal;
    public $eventid;
    public $patientfirstname;
    public $patientlastname;
    public $patientemail;
    public $patientcell;
    private $retrieve;
    private $timezone;


    public static function getSubscribedEvents() : array
    {
        return [
          AppoinmentSetEvent::EVENT_HANDLE  => 'isEventTelehealth'
        ];
    }

    public function __construct()
    {
        $db = new Container();
        $this->retrieve = $db->getDatabase();
        $this->timezone = $this->retrieve->getTimeZone();
    }

    public function isEventTelehealth(AppoinmentSetEvent $event)
    {
        $appointmentdata = $event->givenAppointmentData();
        if (stristr($appointmentdata['form_title'], 'telehealth')) {
                               $pid = $appointmentdata['form_pid'];
                         $comm_data = $this->retrieve->getPatientDetails($pid);
                           $patient = explode(",", $appointmentdata['form_patient']);
              //populate objects for the call to the create session API
                   $this->caller_id = $GLOBALS['unique_installation_id'];
                $this->country_code = $GLOBALS['phone_country_code'];
                     $this->eventid = $event->eid;
            $this->patientfirstname = $patient[0];
             $this->patientlastname = $patient[1];
                     $eventdatetime = $appointmentdata['selected_date'] . " " . $appointmentdata['form_hour'] . ":" . $appointmentdata['form_minute'] . ":00";
            $this->eventdatetimeutc = $this->setEventUtcTime($eventdatetime);
          $this->eventdatetimelocal = $this->setEventLocalTime($eventdatetime);
                $this->patientemail = $comm_data['email'];
                             $phone = preg_replace('/[\s-]+', '', $comm_data['phone_cell']);
                 $this->patientcell = "+" . $GLOBALS['phone_country_code'] . $phone;

            //$creatsession = new AppDispatch();
            //$creatsession->apiRequestSession('', '', 'createsession');
            file_put_contents("/var/www/html/errors/event_data.txt", $this->eventdatetimelocal . " " .  print_r($comm_data));
        }
    }

    private function setEventUtcTime($eventdatetime)
    {
        $z = 'UTC';
        $format = "Y-m-d\TH:i:s\Z";
        $date = date_create($eventdatetime, new DateTimeZone($this->timezone));
        $date->setTimezone(new DateTimeZone($z));
        return $date->format($format);
    }

    private function setEventLocalTime($eventdatetime)
    {
        $newDateTime = date_create($eventdatetime, new DateTimeZone($this->timezone));
        return $newDateTime->format("Y-m-d\TH:i:s");
    }
}
