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

use OpenEMR\Modules\LifeMesh\Container;
use OpenEMR\Core\Header;

$getcredentals = sqlQuery("select username, password from lifemesh_account");
$getaccountsummary = new Container();
$password = $getaccountsummary->getDatabase();
$pass = $password->cryptoGen->decryptStandard($getcredentals['password']);
$summaryurl = $getaccountsummary->getAppDispatch();
$url = 'accountSummary';
$data = $summaryurl->apiRequest($getcredentals['username'], $pass, $url);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo xlt('Account Summary') ?></title>
    <?php Header::setupHeader(); ?>
</head>
<body>
<div class="container">
<h2><?php echo xlt('Account Summary') ?></h2>
    <?php
        $j_data = json_decode($data, true);
    ?>
    <table class="table">
        <tr>
            <th><?php echo xlt('Subscription Start')?></th>
            <th><?php echo xlt('Billing Period Start')?></th>
            <th><?php echo xlt('Billing Period Ended')?></th>
            <th><?php echo xlt('Ended At')?></th>
            <th><?php echo xlt('Status')?></th>
            <th><?php echo xlt('Session Count')?></th>
        </tr>
        <tr>
            <td><?php print $j_data['subscription_start']; ?></td>
            <td><?php print $j_data['billing_period_start']; ?></td>
            <td><?php print $j_data['billing_period_end']; ?></td>
            <td><?php print $j_data['ended_at']; ?></td>
            <td><?php print $j_data['status']; ?></td>
            <td><?php print $j_data['session_count']; ?></td>
        </tr>
    </table>

</div>
</body>
</html>


