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
require_once dirname(__FILE__, 2) . "/controller/Container.php";

use OpenEMR\Common\Acl\AclMain;
use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Modules\LifeMesh\Container;
use OpenEMR\Core\Header;

if (!AclMain::aclCheckCore('admin', 'manage_modules')) {
    echo xlt('Not Authorized');
    exit;
}

$getcredentals = sqlQuery("select `username`, `password` from `lifemesh_account`");
if ($getcredentals['username'] == '') {
    die('You are not logged in');
}
$getaccountsummary = new Container();
$password = $getaccountsummary->getDatabase();
$pass = $password->cryptoGen->decryptStandard($getcredentals['password']);
$summaryurl = $getaccountsummary->getAppDispatch();
$url = 'accountSummary';
$data = $summaryurl->apiRequest($getcredentals['username'], $pass, $url);
$url = 'wipeaccount.php';
$reset_cancel_url = 'account_reset_cancel.php';
$setup = '../moduleConfig.php';
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
    <div id="summary">
        <p><strong>Account</strong><br>
        <?php echo text($getcredentals['username']); ?></p>
        <p></p>
        <p><strong>Account Status</strong><br>
        <?php echo ($j_data['status'] == "active") ? "Subscription is active" : "Subscription is not active"; ?></p>
        <p></p>
        <p><strong>Billed Telehealth Sessions this Billing Cycle</strong><br>
        <?php print text($j_data['session_count']); ?></p>
        <p></p>
        <p><strong>Billing Cycle Ends</strong><br>
        <?php print text(gmdate("Y-m-d TH:i:s\Z", $j_data['billing_period_end'])); ?></p>
    </div>
    <div id="plans">
        <p><strong>Telehealth Pricing Tiers</strong><br>
        First 100 Telehealth Sessions costs $99.00<br>
        Next 101 - 200 costs $119.00<br>
        Next 201 - 300 costs $159.00<br>
        Next 301 - 500 costs $279.00<br>
        Next 501 - 750 costs $249.00<br>
        Next 751 sessions and beyond costs $0.75/session</p>
    </div>
    <div id="acctmgr">
        <p></p>
        <p>Reset account password <button class="btn btn-primary" onclick="resetPassword()">Click Here</button></p>
        <?php if ($j_data['status'] == "active") { ?>
            <p>Do you want to cancel your subscription? <button class="btn btn-primary" onclick="cancelSubscription()">Click Here</button></p>
        <?php } else { ?>
            <p>Don't have an active subscription? <a class="btn btn-primary" href="../stripe/client/service.php" target="_blank">Click Here</a></p>
        <?php } ?>
        <p>Sign out <button class="btn btn-primary" onclick="signOut()">Click Here</button></p>
    </div>
</div>
</body>
<script>
    const token = <?php echo js_escape(CsrfUtils::collectCsrfToken()); ?>;
    const reset_url = <?php echo js_escape($reset_cancel_url . '?acct=reset&token='); ?>;
    const cancel_url = <?php echo js_escape($reset_cancel_url . '?acct=cancel&token='); ?>;
    const url = <?php echo js_escape($url . '?token='); ?>;
    const redirect = <?php echo js_escape($setup); ?>;

    async function cancelSubscription() {
        let response = await fetch(cancel_url + encodeURIComponent(token));
        let result = await response.text();
        if (result != '404') {
            $.ajax({
                url: url + encodeURIComponent(token),
                type: 'GET',
                success: function (response) {
                    alert('Account Cancellation Complete');
                    window.location = redirect;
                }
            })
        } else {
            alert('Account cancellation failed ' + result);
        }
    }

    async function signOut() {
        $.ajax({
            url: url + encodeURIComponent(token),
            type: 'GET',
            success: function (response) {
                alert('Account Sign out Complete');
                window.location = redirect;
            }
        })
    }

    async function resetPassword() {
        let response = await fetch(reset_url + encodeURIComponent(token));
        let result = await response.text();
        if (result == 'complete') {
                $.ajax({
                    url: url + encodeURIComponent(token),
                    type: 'GET',
                    success:function(response){
                        alert('Close account page and check your email for new password');
                    window.location = redirect;
                    }
                })
        } else {
            alert(response.statusText + ' Account Reset Failed')
        }
    }
</script>
</html>


