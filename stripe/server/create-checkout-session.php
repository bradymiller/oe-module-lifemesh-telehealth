<?php

/*
 *
 * @package     OpenEMR Telehealth Module
 * @link        https://lifemesh.ai/telehealth/
 *
 * @author      Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright   Copyright (c) 2021 Lifemesh Corp <telehealth@lifemesh.ai>
 * @license     GNU General Public License 3
 *
 */

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;

require_once dirname(__FILE__, 6) . "/globals.php";
require_once dirname(__FILE__, 3) . '/vendor/autoload.php';

use OpenEMR\Modules\LifeMesh\Container;

$createCheckout = new Container();
$email = $_POST['email'];

// Create new Checkout Session for the order
// Other optional params include:
// [billing_address_collection] - to display billing address details on the page
// [customer] - if you have an existing Stripe Customer ID
// [payment_intent_data] - lets capture the payment later
// [customer_email] - lets you prefill the email input in the form
// For full details see https://stripe.com/docs/api/checkout/sessions/create

// ?session_id={CHECKOUT_SESSION_ID} means the redirect will have the session ID set as a query param
$checkout_session = $createCheckout->getAppDispatch()->getStripeUrl('createCheckoutSessionUrl', $email);

$checkout_session_url = json_decode($checkout_session);
$url = get_object_vars($checkout_session_url);

header("HTTP/1.1 303 See Other");
header("Location: " . $url['checkout_url'] );
