<?php  
//for africastalking  
$phonenumber = $_GET['MSISDN'];  
$sessionID = $_GET['sessionId'];  
$servicecode = $_GET['serviceCode'];  
$ussdString = $_GET['text'];  
  
//create data fields  
include("config.php");

  
//N/B: on going live we will change the GET[] method to POST[] (that is how africastalking do their stuff)  
$level =0;  
$ussdString=  str_replace("#", "*", $ussdString);
$ussdString_explode = explode("*", $ussdString);  
if($ussdString != ""){  
$ussdString=  str_replace("#", "*", $ussdString);  
$ussdString_explode = explode("*", $ussdString);  
$level = count($ussdString_explode);  
}  
if ($level==0){
	include("config.php");
  $login=mysqli_num_rows(mysqli_query($conn,"select * from `farmers` where `phone`='$phonenumber'"));
 if($login !=0){
    login_menu();
 }else{
   displaymenu(); 
 }
 
}  
function displaymenu(){  
$ussd_text="CON Welcome, TEA FARMER . <br/>1. Register as a Tea Farmer<br/>";  
ussd_proceed($ussd_text);  
}  
function login_menu(){  
$ussd_text="CON Welcome, TEA FARMER . Enter your pin";  
ussd_proceed($ussd_text);  
}  
function ussd_proceed ($ussd_text){  
echo $ussd_text;  
//exit(0);  
}  
if ($level>0){  
	if($ussdString_explode[0] == "1"){
    register($ussdString_explode,$phonenumber); 
	}else {
		# code...
		$pin=$ussdString_explode[0];
		$login=mysqli_num_rows(mysqli_query($conn,"select * from `farmers` where `pin`='$pin'"));
		if($login !=0)
		{
			login($ussdString_explode,$phonenumber);
		}else{
          $ussd_text="END  Invalid input ";  
          ussd_proceed($ussd_text); 
		}
		 
	}

}  
  

function register($details,$phone){  
  
if (count($details)==1){  
$ussd_text="CON <br/> Enter your Buying center";  
ussd_proceed($ussd_text);  
} else if (count($details)==2){  
$ussd_text="CON <br/> Enter your farmer Registration No";  
ussd_proceed($ussd_text);  
} else if (count($details)==3){  
$ussd_text="CON <br/> Enter your ID";  
ussd_proceed($ussd_text);  
}  
else if(count($details) == 4){  
$ussd_text = "CON <br/> Set your pin";  
ussd_proceed($ussd_text);  
}else if(count($details) == 5){  
$ussd_text = "CON <br/> Confirm your pin";  
ussd_proceed($ussd_text);  
}else if(count($details) == 6){  
$ussd_text = "CON <br/>1. Accept registration <br/> 2. Cancel <br/>";  
ussd_proceed($ussd_text);  
}else if(count($details) == 7){  
$Buying_center=$details[1];  
$farmer_No=$details[2]; 
$farmer_Id=$details[3];  
$pin=$details[4];  
$confirmPin=$details[5];  
$acceptDeny=$details[6];  
$phonenumber = $_GET['MSISDN'];  
if($acceptDeny=="1"){  
// Check if the pin matches
if($pin !==$confirmPin){
$ussd_text = "END <br/>Sorry! Your Pin Didn't mutch <br/>";  
ussd_proceed($ussd_text);
}else{
//=================Do your business logic here===========================  
//Remember to put "END" at the start of each echo statement that comes here  
include("config.php");
$login=mysqli_num_rows(mysqli_query($conn,"select * from `farmers` where `phone`='$phone'"));
if($login !=0)
{
$ussd_text = "END <br/> Sorry!.That Mobile Number is already registered <br/>";  
ussd_proceed($ussd_text);  
}
else
{
$date=date("d-m-y h:i:s");
$q=mysqli_query($conn,"insert into `farmers` (`date_entered`,`buying_center`,`farmer_No`,`farmer_Id`,`pin`,`phone`) values ('$date','$Buying_center','$farmer_No','$farmer_Id','$pin','$phonenumber')");
if($q)
{
$ussd_text = "END <br/>Congrats! You have registered successfully with the following details <br/>
Buying_center: " . $Buying_center . "<br>" .  
"Farmer No: " . $farmer_No. "<br>" .  
"Your Id: " . $farmer_Id . "<br>" .  
"Mobile No: " .$phonenumber. "<br>" .
"Pin (Encrypted): " . md5($pin) . "<br>";    
ussd_proceed($ussd_text);
}
else
{
$ussd_text = "END <br/>Oops! Something went wrong.Please try again. <br/>".$phonenumber;  
ussd_proceed($ussd_text);
}
}
}
}else{//Choice is cancel  
$ussd_text = "END Your session is over";  
ussd_proceed($ussd_text);  
}  
  
  
}  
}  
  
function login($details,$phone){  

if(count($details)==1){
$ussd_text="CON <br/> Please reply with.<br/> 1. Check Balance <br/>2.Redeem Tea KG <br/>3. Check Statement <br/> 4. Register for Fertalizer <br/>5.Buy tea leaves";  
ussd_proceed($ussd_text);  
}

if($details[1]==1){
    balance($details,$phone);
}elseif ($details[1]==2) {
	# code...
	redeem($details,$phone);

}elseif ($details[1]==3) {
	statement($details,$phone);
	# code...

}


}



// function for checking balance
function balance($details,$phone){
	if(count($details)==1){
       $ussd_text="CON <br/> Enter your ID statement";  
     ussd_proceed($ussd_text); 

	}elseif (count($details)==2) {
		# code...
		$ussd_text="CON <br/> Enter your NAME";  
    ussd_proceed($ussd_text); 
}elseif (count($details)==3) {
		# code...
		$ussd_text="CON <br/> Enter your second NAME";  
    ussd_proceed($ussd_text); 
}
	}
	

// function for redeem
function redeem($details,$phone){
		 $ussd_text="CON <br/> Enter Number of Kg To redeem";  
		ussd_proceed($ussd_text); 
	
    
}

// function for checking balance
function statement($details,$phone){
	$ussd_text="CON <br/> Enter your PIN";  
    ussd_proceed($ussd_text); 
}
// registration for fertalizer registration
function fertalizer($details,$phone){
	$ussd_text="CON <br/> Enter No of Kg";  
    ussd_proceed($ussd_text); 
}





?>   