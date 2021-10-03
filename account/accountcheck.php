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

require_once "../../../../globals.php";
require_once "../controller/Container.php";

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Modules\LifeMesh\Container;


if (!empty($_POST)) {
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token"])) {
        CsrfUtils::csrfNotVerified();
    }
}

$username = $_POST['username'];
$password = $_POST['password'];

$getcontainer = new Container();
$checkaccount = $getcontainer->getAppDispatch();
$url = 'accountCheck';
$accountisvalid = $checkaccount->apiRequest($username, $password, $url);

if (($checkaccount->getStatus() === 200 && $accountisvalid === true) ||
    ($checkaccount->getStatus() === 261 && $accountisvalid === false)) {
    // Pass when valid with active subscription (status is 200 and accountisvalid is true) or
    //  without active subscription (status is 261 and accountisvalid is false)
    $savecredentials = $getcontainer->getDatabase();
    $savecredentials->saveUserInformation($username, $password);
} else {
    echo text($checkaccount->getStatusMessage());
    exit;
}

header('Location: accountsummary.php');




