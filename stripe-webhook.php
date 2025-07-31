<?php
require 'vendor/autoload.php'; // Stripe PHP SDK
require 'config.php';

// Set your secret key securely
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Securely retrieve the webhook secret
$endpoint_secret = STRIPE_WEBHOOK_SECRET;

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

// Handle successful checkout
if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    $customerEmail = $session->customer_details->email ?? 'no-email';
    $total = $session->amount_total / 100;

    file_put_contents('webhook-log.txt', "✅ Order from $customerEmail for $$total\n", FILE_APPEND);
}

http_response_code(200);
