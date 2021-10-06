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

require_once "../../../globals.php";
require_once dirname(__FILE__)."/controller/Container.php";

use OpenEMR\Common\Acl\AclMain;
use OpenEMR\Core\Header;
use OpenEMR\Modules\LifeMesh\Container;

if (!AclMain::aclCheckCore('admin', 'manage_modules')) {
    echo xlt('Not Authorized');
    exit;
}

$installdatabasetable = new Container();
$loadTable = $installdatabasetable->getDatabase();
$status = $loadTable->doesTableExist();

if ($status == "exist") {
 $accounthaslogin = sqlQuery("SELECT `username` FROM `lifemesh_account`");
}
if (!empty($accounthaslogin['username'])) {
    header('Location: account/accountsummary.php');
}

?>
<!DOCTYPE html>
<head>
    <?php Header::setupHeader(); ?>
    <meta charset="utf-8" />
    <title>Telehealth Service</title>
    <style>
        .btnlogin {
            background: #3498db;
            background-image: -webkit-linear-gradient(top, #3498db, #2980b9);
            background-image: -moz-linear-gradient(top, #3498db, #2980b9);
            background-image: -ms-linear-gradient(top, #3498db, #2980b9);
            background-image: -o-linear-gradient(top, #3498db, #2980b9);
            background-image: linear-gradient(to bottom, #3498db, #2980b9);
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            border-radius: 8px;
            font-family: Arial;
            color: #ffffff;
            font-size: 20px;
            padding: 10px 20px 10px 18px;
            text-decoration: none;
        }

        .btnlogin:hover {
            background: #3cb0fd;
            background-image: -webkit-linear-gradient(top, #3cb0fd, #3498db);
            background-image: -moz-linear-gradient(top, #3cb0fd, #3498db);
            background-image: -ms-linear-gradient(top, #3cb0fd, #3498db);
            background-image: -o-linear-gradient(top, #3cb0fd, #3498db);
            background-image: linear-gradient(to bottom, #3cb0fd, #3498db);
            text-decoration: none;
        }

        .btnsubscribe {
            background: #3fe896;
            background-image: -webkit-linear-gradient(top, #3fe896, #2bb85c);
            background-image: -moz-linear-gradient(top, #3fe896, #2bb85c);
            background-image: -ms-linear-gradient(top, #3fe896, #2bb85c);
            background-image: -o-linear-gradient(top, #3fe896, #2bb85c);
            background-image: linear-gradient(to bottom, #3fe896, #2bb85c);
            -webkit-border-radius: 8px;
            -moz-border-radius: 8px;
            border-radius: 8px;
            font-family: Arial;
            color: #ffffff;
            font-size: 20px;
            padding: 10px 20px 10px 18px;
            text-decoration: none;
        }

        .btnsubscribe:hover {
            background: #3cfcb6;
            background-image: -webkit-linear-gradient(top, #3cfcb6, #34d976);
            background-image: -moz-linear-gradient(top, #3cfcb6, #34d976);
            background-image: -ms-linear-gradient(top, #3cfcb6, #34d976);
            background-image: -o-linear-gradient(top, #3cfcb6, #34d976);
            background-image: linear-gradient(to bottom, #3cfcb6, #34d976);
            text-decoration: none;
        }

        .button1 {
            padding: 1em;
        }

        .button2 {
            padding: 1em;
        }
        #login {
            width: 250px;
        }

        #subscribe {
            width: 250px;
        }

    </style>
</head>
<body>
<div class="container">
    <img src="account/images/lifemesh_logo.png"></img>

    <div class="button1">
        <a id="theloginbutton" href="account/index.php"><button id="login" class="btnlogin">Login</button></a>
    </div>
    <div class="button2">
        <a id="thesubscribebutton" href="stripe/server/create-checkout-session.php" target="_blank"><button id="subscribe" class="btnsubscribe">Subscribe</button></a>
    </div>
</div>
</body>
<script>

</script>
</html>

