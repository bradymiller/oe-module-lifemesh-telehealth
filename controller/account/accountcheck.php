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
require_once "../controller/AppDispatch.php";
require_once "../controller/Database.php";

use OpenEMR\Common\Csrf\CsrfUtils;


if (!empty($_POST)) {
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token"])) {
        CsrfUtils::csrfNotVerified();
    }
}

$username = $_POST['username'];
$password = $_POST['password'];

$checkaccount = new OpenEMR\Modules\LifeMesh\AppDispatch();
$accountisvalid = $checkaccount->accountcheck($username, $password);

//check if DB table exist and create it if it does not exist.
$doestableexist = new OpenEMR\Modules\LifeMesh\Database();
$res = $doestableexist->doesTableExist();
if(!$res) {
    echo "Table needs to be installed.";
}



