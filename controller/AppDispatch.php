<?php

/*
 * @package      OpenEMR
 * @link               https://www.open-emr.org
 *
 * @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright Copyright (c) 2021 Sherwin Gaddis <sherwingaddis@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

namespace OpenEMR\Modules\LifeMesh;

//require_once "../../../globals.php";


/**
 * Class AppDispatch
 * @package OpenEMR\Modules\LifeMesh
 */
class AppDispatch
{
    public $accountCheck;
    public $accountSummary;
    public $createSession;

    /**
     * AppDispatch constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $username
     * @param $password
     * @param $url
     * @return string
     */
    public function apiRequest($username, $password, $url)
    {
        $data = base64_encode($username . ':' . $password);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->setUrl($url)); //dynamically set the url for the api request
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $data]);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $response = curl_exec($curl);

        curl_close($curl);

        if ($url == 'accountCheck') {
            if ($status === 0) {
                return true;
            } else {
                echo $status;
                die(" An Error occured. Username or Password is incorrect. Please contact Lifemesh ");
            }
        }
        if ($url == 'accountSummary') {
            return $response;
        }
    }

    /**
     * @param $value
     * @return string|null
     * set URL values based on the call to action
     */
    private function setUrl($value)
    {
        switch ($value) {
            case "accountCheck":
                return 'https://huzz90crca.execute-api.us-east-1.amazonaws.com/account_check';

            case "accountSummary":
                return 'https://huzz90crca.execute-api.us-east-1.amazonaws.com/account_summary';

            default:
                return NULL;
        }
    }
}
