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


require_once dirname(__DIR__, 3) . "/globals.php";
require_once "controller/Container.php";

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Common\Uuid\UniqueInstallationUuid;

if (!CsrfUtils::verifyCsrfToken($_GET["csrf_token"])) {
    CsrfUtils::csrfNotVerified();
}

// no acl check since this needs to be accessed by entire practice from calendar

/** @var TYPE_NAME $eventid */
$eventid = $_GET['eid'];

$action = new OpenEMR\Modules\LifeMesh\Container();

$credentials = $action->getDatabase();

$accountinfo = $credentials->getCredentials();

$encryptedaccountinfo = base64_encode($accountinfo[1] . ":" . $accountinfo[0]);

$cancel = $action->getAppDispatch();

$uniqueInstallationId = UniqueInstallationUuid::getUniqueInstallationUuid();

echo text($cancel->cancelSession($encryptedaccountinfo, $eventid, $uniqueInstallationId,'cancelSession'));
