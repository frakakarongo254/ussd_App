<?php

require 'vendor/autoload.php';
require('AfricasTalkingGateway.php');
use AfricasTalking\SDK\AfricasTalking;


// Set your app credentials
$username   = "sandbox";
$apiKey     = "eff5264dd30db0b4e19663827f0a82c1a7e2fdc60fe90249a36534942f8ed7e8";

// Initialize the SDK
$AT         = new AfricasTalking($username, $apiKey);

// Get the SMS service

//$sms       = $AT->sms();

   
 
function sendMessage() {

function  sendingMessageAfterRegistration($Buying_center,$farmer_RegNo,$farmer_National_Id,$phonenumber){
        global $AT;
    $sms = $AT->sms();
     $phone= substr($phonenumber, 0, 0) .'+254'. substr($phonenumber, 3, 9) ;
    $recipients =  $phone;
    // Set the numbers you want to send to in international format
    
    // Set your message
    $message    = "Congratulation Tea Farmer, You have successfuly registered with the following details 
                    Buying_center: " . $Buying_center . "," .  
                    "Farmer No: " . $farmer_RegNo. "," .  
                    "Your Id: " . $farmer_National_Id . "," ;  
    // Set your shortCode or senderId
    $from       = "KTDA";
    
    try {
        // Thats it, hit send and we'll take care of the rest
        $result = $sms ->send([
            'to'      => $recipients,
            'message' => $message,
            'from'    => $from
        ]);

        print_r($result);
    } catch (Exception $e) {
        echo "Error: ".$e.getMessage();
    }
}
}

sendMessage();

