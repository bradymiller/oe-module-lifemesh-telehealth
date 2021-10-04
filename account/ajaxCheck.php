<?php

/**
 * ajaxCheck.php
 *
 * @package   OpenEMR
 * @link      https://www.open-emr.org
 * @author    Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2021 Brady Miller <brady.g.miller@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

require_once "../../../../globals.php";
require_once "../controller/Container.php";

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Modules\LifeMesh\Container;

header('Content-type: application/json');

if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token"])) {
    CsrfUtils::csrfNotVerified();
}

// check for existence and status of lifemesh account
$container = new Container();
$credentials =  $container->getDatabase()->getCredentials();
$isCredentials = (!empty($credentials) && !empty($credentials[0]) && !empty($credentials[1]));
$status = [];
if ($isCredentials) {
    $app = $container->getAppDispatch();
    $subscriberStatus = $app->apiRequest($credentials[1], $credentials[0], 'accountCheck');
    if (!$subscriberStatus) {
        $status['status'] = "no";
        $status['statusMessage'] = "Not working. " . $app->getStatusMessage();
    } else {
        $status['status'] = "ok";
        $status['statusMessage'] = "OK";
    }
} else {
    $status['status'] = "no";
    $status['statusMessage'] = "Not functional. A subscriber is not configured in the Lifemesh Telehealth module.";
}

echo json_encode($status);
exit;
