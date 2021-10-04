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

require_once dirname(__FILE__, 5) . "/globals.php";

use OpenEMR\Modules\LifeMesh\Container;
use OpenEMR\Common\Csrf\CsrfUtils;

if (!CsrfUtils::verifyCsrfToken($_GET["token"])) {
    CsrfUtils::csrfNotVerified();
}

function credentials(): string
{
    $getaccountsummary = new Container();
    $getcredentals = sqlQuery("select username, password from lifemesh_account");
    $password = $getaccountsummary->getDatabase();
    $username = $getcredentals['username'];
    $pass = $password->cryptoGen->decryptStandard($getcredentals['password']);
    return base64_encode($username . ':' . $pass);
}

function cancelSubscription()
{
    $getaccountsummary = new Container();
    $encryptedaccountinfo = credentials();
    $docancelation = $getaccountsummary->getAppDispatch();
    echo $docancelation->cancelSubscription($encryptedaccountinfo, 'cancelSubscription');
}

function resetPassword()
{
    $getaccountsummary = new Container();
    $encryptedaccountinfo = credentials();
    $doreset = $getaccountsummary->getAppDispatch();
    echo $doreset->resetPassword($encryptedaccountinfo, 'resetPassword');
}

if ($_GET['acct'] == 'reset') {
    echo resetPassword();
}

if ($_GET['acct'] == 'cancel') {
    echo cancelSubscription();
}

