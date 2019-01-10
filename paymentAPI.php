<?php
use AfricasTalking\SDK\AfricasTalking;

// Set your app credentials
$username   = "sandbox";
$apiKey     = "eff5264dd30db0b4e19663827f0a82c1a7e2fdc60fe90249a36534942f8ed7e8";


// Initialize the SDK
global $AT;
//global $payments;
$AT         = new AfricasTalking($username, $apiKey);

// Get the payments service
$payments   = $AT->payments();

function initiateMobileB2C() {
    // Set the name of your Africa's Talking payment product

function payFarmerRedeemedKg($phone,$moneyToReceive){
    global $AT;
    $payments   = $AT->payments();
    $productName  = "FRAKA";
  $phoneNumber = substr($phone, 0, 0) .'+254'. substr($phone, 3, 9) ;
    // Set your mobile b2c recipients
$recipients = [[
        "phoneNumber"  => $phoneNumber,
        "currencyCode" => "KES",
        "amount"       => 100.50,
        "reason"       => $payments::REASON["SALARY"],
        "metadata"     => [
            "name"     => "John Doe"
        ]
    ]];
    try {
        // That's it, hit send and we'll take care of the rest
        $results = $payments->mobileB2C([
            "productName" => $productName,
            "recipients"  => $recipients
        ]);

        print_r($results);
    } catch(Exception $e) {
        echo "Error: ".$e.getMessage();
    }
}
}

initiateMobileB2C();