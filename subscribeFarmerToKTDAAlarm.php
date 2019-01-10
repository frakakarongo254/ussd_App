<?php
require 'vendor/autoload.php';
//require('AfricasTalkingGateway.php');
use AfricasTalking\SDK\AfricasTalking;
// Set your app credentials
$username   = "sandbox";
$apiKey     = "eff5264dd30db0b4e19663827f0a82c1a7e2fdc60fe90249a36534942f8ed7e8";


// Initialize the SDK
$AT       = new AfricasTalking($username, $apiKey);

// Get the sms service
$sms      = $AT->sms();

// Get the token service
$token    = $AT->token();

function createSubscription() {
	function subscribeFarmerToKTDAAlarm($phoneToSubscribe){
		 global $AT;
		 global $phoneNumber;
		$sms      = $AT->sms();

// Get the token service
$token    = $AT->token();

    // Set your premium product shortCode and keyword
    $shortCode   = 'myPremiumShortCode';
    $keyword     = 'myPremiumKeyword';

    // Set the phone number you're subscribing
    $phoneNumber = $phoneToSubscribe;

    try {
        // Get a checkoutToken for the phone number you're subscribing
        $checkoutTokenResult = $token->createCheckoutToken([
            'phoneNumber'    => phoneNumber
        ]);

        $checkoutToken       = $checkoutTokenResult->token;

        // Create the subscription
        $result = $sms->createSubscription([
            'shortCode'      => $shortCode,
            'keyword'        => $keyword,
            'phoneNumber'    => $phoneNumber,
            'checkoutToken'  => $checkoutToken
        ]);

        print_r($result);
    } catch (Exception $e) {
        echo "Error: ".$e.getMessage();
    }
}
}

createSubscription();
?>