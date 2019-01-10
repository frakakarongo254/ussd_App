<?php
// Reads the variables sent via POST from our gateway
$phonenumber = $_GET['MSISDN'];  
$sessionID = $_GET['sessionId'];  
$servicecode = $_GET['serviceCode'];  
$ussdString = $_GET['text'];  

//N/B: on going live we will change the GET[] method to POST[] (that is how africastalking do their stuff)  
$level =0;  
  
if($ussdString != ""){  
$ussdString=  str_replace("#", "*", $ussdString);  
$ussdString_explode = explode("*", $ussdString);  
$level = count($ussdString_explode);  
} 
if ($level==0){
 if($phonenumber =='0726172579'){
 $ussd_text="CON Welcome, TEA FARMER 1. Login<br/>";  
//ussd_proceed($ussd_text);
 $ussdString_explode[0]=0;
    login($ussdString_explode,$phonenumber);
 }else{
 $ussd_text="CON Welcome, TEA FARMER . Please reply with; <br/>1. Register<br/>";  
ussd_proceed($ussd_text);
   //displaymenu(); 
 }
}
function login($details,$phone){  
if (count($details)==1){  
$ussd_text="CON <br/> Enter your PIN";  
ussd_proceed($ussd_text);  
}  
else if(count($details)==2){  
$pin=$details[1];  


//$coffee_kg=$details[2]; 
include("config.php"); 
$login=mysqli_num_rows(mysqli_query($conn,"select * from `farmers` where `pin`='$pin'"));
	if($login !=0)
	{
		
			$ussd_text="CON <br/> Please reply with.<br/> 1. Check Balance <br/>2.Redeem Tea KG <br/>3. Check Statement <br/> 4. Register for Fertalizer";  
            ussd_proceed($ussd_text);  

	}
	else
	{
	$ussd_text = "END <br/>Invalid PIN number";  
    ussd_proceed($ussd_text);
	}
	 	
}elseif(count($details) ==3) {
		 	// $phonenumber = $_GET['MSISDN']; 
		 	  
			if ($details[2]==1) {
				//check balance
				if(count($details)==3){
					$ussd_text="CON <br/> Enter Number of Kg To redeem";  
					ussd_proceed($ussd_text); 
				}elseif (count($details)==4) {
					$ussd_text="CON <br/> Enter Your Pin";  
					ussd_proceed($ussd_text);
				}elseif (count($details)==5) {
					$ussd_text="CON <br/> Enter Your Name";  
					ussd_proceed($ussd_text);
				}

			}elseif ($details[2]==2){
				// Redeem Kg
				
					$ussd_text="CON <br/> Enter Number of Kg To redeem";  
					ussd_proceed($ussd_text); 
				if (count($details)==1) {
					$ussd_text="CON <br/> Enter Your Pin";  
					ussd_proceed($ussd_text);
				}elseif (count($details)==2) {
					$ussd_text="CON <br/> Enter Your Name";  
					ussd_proceed($ussd_text);
				}
			}elseif($details[2]==3){
                #statement
                $ussd_text="CON <br/> Enter Your statement";  
					ussd_proceed($ussd_text);
			}elseif ($details[2]==4) {
				# regester for fertalizer
				$ussd_text="CON <br/> Enter Your fertalizer";  
					ussd_proceed($ussd_text);
			}

			 
			
		}  
			
}  
 function ussd_proceed ($ussd_text){  
 echo $ussd_text;  
//exit(0);  

}


?>