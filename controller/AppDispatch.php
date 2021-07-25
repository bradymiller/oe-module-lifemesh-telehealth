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

use OpenEMR\Common\Crypto\CryptoGen;
use OpenEMR\Common\Http\oeHttp;
use OpenEMR\Common\Http\oeHttpRequest;


class AppDispatch
{
    public function __construct()
    {
        //do epic stuff here!!
    }

    public function accountcheck($username, $password)
    {
        $curl = curl_init();
        $data = base64_encode($username . ':' . $password);
        curl_setopt($curl, CURLOPT_URL, 'https://huzz90crca.execute-api.us-east-1.amazonaws.com/account_check');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, 10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $data]);

        $response = curl_exec($curl);

        curl_close($curl);

        if ($response == '"Your credentials are valid and an active subscription is found for this account."') {
            //$db = new Database();
            return "Yes ";
        } else {
            die(" An Error occured. Username or Password is incorrect. Please contact Lifemesh ");
        }
    }


}
