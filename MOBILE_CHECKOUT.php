<?php
require 'vendor/autoload.php';
//require('AfricasTalkingGateway.php');
use AfricasTalking\SDK\AfricasTalking;

// Set your app credentials
$username   = "sandbox";
$apiKey     = "eff5264dd30db0b4e19663827f0a82c1a7e2fdc60fe90249a36534942f8ed7e8";


// Initialize the SDK
global $AT;
$AT         = new AfricasTalking($username, $apiKey);

// Get the payments service
$payments   = $AT->payments();

function initiateMobileCheckout() {
    // Set the name of your Africa's Talking payment product
    function chargeFarmerForTeaLeaves($phone,$moneyToReceive){
        global $AT;
        $payments   = $AT->payments();

    $productName  = "FRAKA";
  //echo $phone;
   $phoneNumber = substr($phone, 0, 0) .'+254'. substr($phone, 3, 9) ;
    // Set the phone number you want to send to in international format
    // $phoneNumber  = '+254707071957';

    // Set The 3-Letter ISO currency code and the checkout amount
    $currencyCode = "KES";
    $amount       = "100.50";

    // Set any metadata that you would like to send along with this request.
    // This metadata will be included when we send back the final payment notification
    $metadata = [
        "agentId"   => "654",
        "productId" => "321"
    ];

    try {
        // Thats it, hit send and we'll take care of the rest.
        $result = $payments->mobileCheckout([
            "productName"  => $productName,
            "phoneNumber"  => $phoneNumber,
            "currencyCode" => $currencyCode,
            "amount"       => $amount,
            "metadata"     => $metadata
        ]);

        print_r($result);
    } catch(Exception $e) {
        echo "Error: ".$e.getMessage();
    }
}
}

initiateMobileCheckout();