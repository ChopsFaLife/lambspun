<?php
require 'vendor/autoload.php'; // Stripe PHP SDK

// Use your Stripe secret key for verifying events. In test mode use sk_test_...
\Stripe\Stripe::setApiKey('sk_test_REPLACE_WITH_YOUR_SECRET_KEY');

// Replace with your webhook signing secret from the Stripe dashboard
$endpoint_secret = 'whsec_REPLACE_WITH_WEBHOOK_SECRET';

$payload = @file_get_contents("php://input");
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
} catch(\UnexpectedValueException $e) {
    http_response_code(400);
    exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit();
}

// Handle the event
if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;

    // Retrieve metadata (optional: add custom fields during session creation)
    $customerEmail = $session->customer_details->email;
    $total = $session->amount_total / 100;

    // TODO: Retrieve cart/shipping info from DB or metadata
    // For now, log basic info
    file_put_contents('webhook-log.txt', "✅ Order from $customerEmail for $$total\n", FILE_APPEND);
}

http_response_code(200);
