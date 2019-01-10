<?php  
//for africastalking  
$phonenumber = $_GET['MSISDN'];  
$sessionID = $_GET['sessionId'];  
$servicecode = $_GET['serviceCode'];  
$ussdString = $_GET['text'];  
  
  // get farmer regNo with the phone number
$farmerRegNo ="";
$farmer_National_Id="";
//create data fields  
require_once("config.php");
require_once('SMS_SENDING.php');
 require_once('MOBILE_CHECKOUT.php');
 require_once('paymentAPI.php');
 require_once('subscribeFarmerToKTDAAlarm.php');
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
  $phone =substr($phonenumber, 0, 0) .'+254'. substr($phonenumber, 4, 9) ;
  $login=mysqli_num_rows(mysqli_query($conn,"select * from `tea_farmer` where `phone`='$phone'"));
 if($login !== 0){
    login_menu();
 }else{
   displaymenu();
   //login_menu(); 
 }
 
}  
function displaymenu(){  
$ussd_text="CON Welcome, TEA FARMER . <br/>1. Register as a Tea Farmer<br/>";  
ussd_proceed($ussd_text);  
}  
function login_menu(){  
$ussd_text="CON Welcome, TEA FARMER .<br/> Enter your pin";  
ussd_proceed($ussd_text);  
}  
function ussd_proceed ($ussd_text){  
echo $ussd_text;  
//exit(0);  
}  
if ($level>0){ 
    $phone =substr($phonenumber, 0, 0) .'+254'. substr($phonenumber, 4, 9) ;
	$login=mysqli_num_rows(mysqli_query($conn,"select * from `tea_farmer` where `phone`='$phone'"));
	if($ussdString_explode[0] == "1" && $login == 0 ){
    register($ussdString_explode,$phonenumber); 
	}else {
		# code...
		$pin=$ussdString_explode[0];
		$query=mysqli_query($conn,"select * from `tea_farmer` where `pin`='$pin' && `phone`='$phone'");
		$login=mysqli_num_rows($query);
		$row = mysqli_fetch_array($query);
		//get all necessary data for farmer to send with login function and use them later in login function 
      $farmerRegNo =$row['farmer_RegNo'];
      $farmer_National_Id =$row['farmer_National_Id'];
      $buyingCenter=$row['buying_center'];
		if($login !==0)
		{
			login($ussdString_explode,$phonenumber,$farmerRegNo,$farmer_National_Id,$buyingCenter);
		}else{
          $ussd_text="END  Invalid Pin ";  
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
}elseif (count($details)==3) {
	# code...
	$ussd_text="CON <br/> Enter your Number of Tea plant";  
     ussd_proceed($ussd_text); 
} else if (count($details)==4){  
$ussd_text="CON <br/> Enter your National ID";  
ussd_proceed($ussd_text);  
}  
else if(count($details) == 5){  
$ussd_text = "CON <br/> Set your pin";  
ussd_proceed($ussd_text);  
}else if(count($details) == 6){  
$ussd_text = "CON <br/> Confirm your pin";  
ussd_proceed($ussd_text);  
}else if(count($details) == 7){  
$ussd_text = "CON <br/>1. Accept registration <br/> 2. Cancel <br/>";  
ussd_proceed($ussd_text);  
}else if(count($details) == 8){  
$Buying_center=$details[1];  
$farmer_RegNo=$details[2]; 
$no_of_tea_plant=$details[3];  
$farmer_National_Id=$details[4];  
$pin=$details[5];  
$confirmPin=$details[6];  
$acceptDeny=$details[7];  
 $phone = $_GET['MSISDN']; 
echo $phonenumber =substr($phone, 0, 0) .'+254'. substr($phone, 4, 9) ; 
if($acceptDeny=="1"){  
// Check if the pin matches
if($pin !==$confirmPin){
$ussd_text = "END <br/>Sorry! Your Pin Didn't mutch <br/>";  
ussd_proceed($ussd_text);
}else{
//=================Do your business logic here===========================  
//Remember to put "END" at the start of each echo statement that comes here  
include("config.php");
// check if phone number is allready registered
$login=mysqli_num_rows(mysqli_query($conn,"select * from `tea_farmer` where `phone`='$phonenumber' && `farmer_RegNo`='$farmer_RegNo' && `farmer_National_Id`='$farmer_National_Id'"));
if($login !=0)
{
$ussd_text = "END <br/> Sorry!.That Mobile Number or Farmer RegNo is already registered <br/>";  
ussd_proceed($ussd_text);  
}
else
{
 $date=date("d-m-y h:i:s");
$q=mysqli_query($conn,"insert into `tea_farmer` (`date_entered`,`buying_center`,`farmer_National_id`,`farmer_RegNo`,`no_Of_Tea_Plant`,`pin`,`phone`) values ('$date','$Buying_center','$farmer_National_Id','$farmer_RegNo','$no_of_tea_plant','$pin','$phonenumber')");
if($q)
{

$Buying_center=$details[1];  
$farmer_RegNo=$details[2]; 
$no_of_tea_plant=$details[3];  
$farmer_National_Id=$details[4];  
$phonenumber = $_GET['MSISDN']; 
// call function to send SMS to the farmer after registration
	sendingMessageAfterRegistration($Buying_center,$farmer_RegNo,$farmer_National_Id,$phonenumber);
$ussd_text = "END <br/>Congrats! You will receive a confirmation message shortlly <br/>";    
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
  
function login($details,$phone,$farmerRegNo,$farmer_National_Id,$buyingCenter){  

if(count($details)==1){
$ussd_text="CON <br/> Please reply with.<br/> 1. Check Balance <br/>2.Redeem Tea KG <br/>3.Buy tea leaves  <br/> 4. Register for Fertilizer <br/>5.Mini Statement <br/>6. Subscribe To KTD Alarm";  
ussd_proceed($ussd_text);  
}
// Start level 2
if(count($details)==2 && $details[1]=='1'){
#Display Check  balance Menu
$ussd_text="CON <br/> Please reply with.<br/> 1. Tea leaves packets for this Month <br/>2. Tea KG Balance <br/>3.Fertilizer bags Balance<br/>4.Back ";  
ussd_proceed($ussd_text); 

}elseif (count($details)==2 && $details[1]=='2') {
# Redeem tea kg
$ussd_text="CON <br/> Enter Number of Kg To redeem";  
ussd_proceed($ussd_text);
}elseif (count($details)==2 && $details[1]=='3') {
# order tea leaves level 3
$ussd_text="CON <br/> Enter Number of packets to order";  
ussd_proceed($ussd_text);
}elseif (count($details)==2 && $details[1]=='4') {
# Get no of fertalizer in kg
$ussd_text="CON <br/> Enter Number of bags of fertilizer to order";  
ussd_proceed($ussd_text);
}elseif (count($details)==2 && $details[1]=='5') {
# request pin to give statement
$ussd_text="CON <br/> Enter Your ID";  
ussd_proceed($ussd_text);
}elseif (count($details)==2 && $details[1]=='6') {
	# enter Id to subscribe to KTDA Alarm
	$ussd_text="CON <br/> Enter Your ID To subscribe";  
ussd_proceed($ussd_text);
}elseif(count($details)==3 && $details[1]=='1' && $details[2]=='1'){
# pin to check tea leaves balance 
//include("config.php");
$ussd_text = "CON <br/>Enter Your ID to check tea leave balance";  
ussd_proceed($ussd_text); 

}elseif(count($details)==4 && $details[1]=='1' && $details[2]=='1'){
  //Display results for tea leaves balance for this month
	$phonenumber = $_GET['MSISDN'];  
	$pin=$details[3];
	//check if id supplied is equal to the farmer national id in the db
	if($pin==$farmer_National_Id){
		include("config.php");
       	// get farmer of this mobile number regNo  before getting balance
	$query=mysqli_query($conn,"select * from `tea_leaves_buying` where  `farmer_RegNo`='$farmerRegNo' ");
	$row = mysqli_fetch_array($query);
	$total_TeaKG=$row['No_of_packets'];
	// 3 is the max tea leaves packets that a farmer can buy
	$balance= 3- $total_TeaKG;
	// Fetch data from db of this farmer with this RegNo
	$ussd_text = "END <br/>Your Tea leaves packets balance for this month is ".$balance;  
	ussd_proceed($ussd_text); 
	}else{
		$ussd_text = "END <br/>Invalid ID " ;  
    ussd_proceed($ussd_text);
	}

}elseif (count($details)==3 && $details[1]=='1' && $details[2]=='2') {
	# Check balance for tea supplied to ktda (unpaid tea kg)
	$ussd_text = "CON <br/>Enter Your ID to check Tea balance " ;  
    ussd_proceed($ussd_text);
}elseif (count($details)==4 && $details[1]=='1' && $details[2]=='2') {
	# Display results for tea balance
	$ID=$details[3];
	// check if id entered is equal to the id in database 
	if($ID==$farmer_National_Id){
	include("config.php");	
	$query=mysqli_query($conn,"select * from `tea_tbl` where `farmer_RegNo`='$farmerRegNo'");
	$row = mysqli_fetch_array($query);
	$available_kg =$row['total_TeaKg'];
	$ussd_text = "END <br/>Your unpaid Tea balance   is ". $available_kg ."  Kg";  
	ussd_proceed($ussd_text);
	}else{
    $ussd_text = "END <br/>Invalid ID " ;  
    ussd_proceed($ussd_text);
	}


}elseif (count($details)==3 && $details[1]=='1' && $details[2]=='3') {
	# Check fertilizer bags for this
	$ussd_text = "CON <br/>Enter Your pin to check Fertilizer bags balance " ;  
    ussd_proceed($ussd_text); 
}elseif (count($details)==4 && $details[1]=='1' && $details[2]=='3') {
	# Display results for fertilizer
		$ID=$details[3];
include("config.php");
// check if pin given is valid
if($ID==$farmer_National_Id){
	$query=mysqli_query($conn,"select * from `fertilizer` where `farmer_RegNo`='$farmerRegNo' ");
	$row = mysqli_fetch_array($query);
	$booked_bags =$row['booked_bags'];
	$balance =10 - $booked_bags;
	$ussd_text = "END <br/>Your fertilizer Balance   is <b>". $balance ."  Bags</b>";  
	ussd_proceed($ussd_text); 
	}else{
	$ussd_text = "END <br/>Invalid ID " ;  
	ussd_proceed($ussd_text);
	}


}elseif (count($details)==3 && $details[1]=='1' && $details[2]=='4') {
	# Move back
//$ussd_text="CON <br/> You are moving back";  
//ussd_proceed($ussd_text);
	login_menu();
	//exit();

}elseif (count($details)==3 && $details[1]=='2') {
	# pin before redeeming
	$ussd_text = "END <br/>Enter your ID";  
      ussd_proceed($ussd_text);
}
elseif(count($details)==4 && $details[1]=='2'){
# check if the tea to Redeem is greater than available in db 
$tea_kg=$details[2];
$ID =$details[3];
include("config.php");
#check if ID is correct
if($ID==$farmer_National_Id){
	// # check if the tea to Redeem is greater than available in db 
$query=mysqli_query($conn,"select * from `tea_tbl` where `farmer_RegNo`='$farmerRegNo'");
$row = mysqli_fetch_array($query);
$available_Teakg=0;
$available_Teakg =$row['total_TeaKg'];
if($tea_kg > $available_Teakg ){
$ussd_text = "END <br/>Sorry you have only ". $available_Teakg ."kg Remaining";  
ussd_proceed($ussd_text); 
}else{
	$phonenumber = $_GET['MSISDN'];  
//confirm before sending money
$ussd_text = "CON <br/>Confirm that you want to redeem ". $tea_kg ." Kg @ Ksh65 per 1Kg <br/> 1.Confirm <br/> 2.Cancel " ;  
ussd_proceed($ussd_text); 
}
}else{
 $ussd_text = "END <br/>Invalid ID " ;  
ussd_proceed($ussd_text);
}

}elseif (count($details)==5 && $details[1]=='2' && $details[4]=='1') {
	//Now redeem tea after confirmatiom
	// send money to the farmer
	$tea_kg=$details[2];
	$moneyToReceive=$tea_kg * 65.50;
	$phone = $_GET['MSISDN'];  
	//pay the redeemed kg to the farmer
	payFarmerRedeemedKg($phone,$moneyToReceive);
    $ussd_text = "END <br/>You Will receive Ksh ".$moneyToReceive ." in your M-pesa Shortly." ;  
   ussd_proceed($ussd_text); 
	# code...
}elseif (count($details)==4 && $details[1]=='2' && $details[3]=='2') {
	# have cancled redeming
	$ussd_text = "END <br/>You have chosen Not to Redeem " ;  
ussd_proceed($ussd_text); 
}elseif (count($details)==3 && $details[1]=='3'  ) {
	$No_of_Packets=$details[2];
	$total_price= $No_of_Packets * 100;
	 #Pin to Buy tea leaves
	$ussd_text = "CON <br/><b> Ksh " . $total_price ." </b>Will be deducted from your M-pesa <br/>1. Confirm <br/> 2.Cancel" ;  
  ussd_proceed($ussd_text); 
}elseif (count($details)==4 && $details[1]=='3' && $details[3]=='1') {
	# pin before deducting money for buying tea leaves
	$ussd_text = "CON <br/>Enter Your ID ";  
    ussd_proceed($ussd_text);
}
elseif (count($details)==5 && $details[1]=='3' && $details[3]=='1' ) {

# Deduct money from M-pesaa if pin is correct
$phonenumber = $_GET['MSISDN'];
$ID=$details[4];
$No_of_Packets=$details[2];
$moneyToDeduct= $No_of_Packets * 100;
include("config.php");
// check if pin given is valid
if($ID==$farmer_National_Id){
	// deduct money for tea buying function to be here
	chargeFarmerForTeaLeaves($phone,$moneyToDeduct);
$ussd_text = "END <br/>Dear Tea Farmer,Please wait as we process";  
ussd_proceed($ussd_text); 
}else{
$ussd_text = "END <br/>Invalid ID " ;  
ussd_proceed($ussd_text);
}


}elseif (count($details)==4 && $details[1]=='3' && $details[3]=='2') {
# cancle not to be charged from M-pesa for tea leaves
$ussd_text = "END <br/>You have cancled" ;  
ussd_proceed($ussd_text);

}elseif (count($details)==3 && $details[1]=='4') {
# Register for fertalizer
$ordered_fertilizer_bags=$details[2];
if($ordered_fertilizer_bags >20){
$ussd_text="CON <br/> You can only order a maximum of 20 bags";  
ussd_proceed($ussd_text);
}else{
	// check if the farmer is already registered for fertilizer
	include('config.php');
	$login=mysqli_num_rows(mysqli_query($conn,"select * from `fertilizer` where `farmer_RegNo`='$farmerRegNo' "));
if($login ==0){
	//Enter data for booking to db  fertilizer
	echo $date=date("d-m-y h:i:s");
	//echo today($date);
	$result=mysqli_query($conn,"insert into  `fertilizer` (`farmer_RegNo`,`booking_date`,`booked_bags`) values('$farmerRegNo','$date','$ordered_fertilizer_bags') ");
	if($result){
	$ussd_text="END <br/> Success!.You will receive a confirmation message with your registration details";  
ussd_proceed($ussd_text);
}else{
	$ussd_text="END <br/> Oops! Something went wrong.Please try again";  
ussd_proceed($ussd_text);
}
}else{
$ussd_text="END <br/> Dear Farmer, You are already registered for fertilizer";  
ussd_proceed($ussd_text);
}

}
	
}
elseif(count($details)==3 && $details[1]=='5'){

# Print statement
$ID= $details[2];
if($ID==$farmer_National_Id){
	// send sms with Mini statement

$ussd_text = "END <br/>Thank you. You will receive a your  statement shortly";  
ussd_proceed($ussd_text); 
}else{
$ussd_text = "END <br/>Invalid ID " ;  
ussd_proceed($ussd_text);
}

}elseif (count($details)==3 && $details[1]=='6') {
	# check if id supplied is corect before subscribing
	$ID=$details[2];
	if($ID==$farmer_National_Id){
// Function to subscribe farmer to KTDA Alerm
#fucnction to be here
//enter into temperlari subscribtion table
include('config.php');
$phone = $_GET['MSISDN']; 
echo "this ".$buyingCenter;
echo $date=date("d-m-y h:i:s");
echo $phoneToSubscribe =substr($phone, 0, 0) .'+254'. substr($phone, 4, 9) ; 
$q=mysqli_query($conn,"insert into `temp_subscription_table` (`date_entered`,`buying_center`,`phone`) values ('$date','$buyingCenter','$phoneToSubscribe')");
if($q){
		subscribeFarmerToKTDAAlarm($phoneToSubscribe);
$ussd_text = "END <br/>Dear farmer! Please wait as we process";  
ussd_proceed($ussd_text); 
}else{
 $ussd_text = "END <br/>Oops! Something went wrong.Please try again";  
ussd_proceed($ussd_text); 
}
}else{
$ussd_text = "END <br/>Invalid ID " ;  
ussd_proceed($ussd_text);
}
}


}



// function for checking balance
function balance($details,$phone){
	$ussd_text="CON <br/> Enter your PIN";  
    ussd_proceed($ussd_text); 
}

// function for redeem
function redeem($details,$phone){
		 
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










