﻿<?php
$count_success = 0;
include('function.php');
if (!isset($_SESSION['email']) || $_SESSION['email']==""){
echo "<script>window.open('index.php','_self')</script>";
}else{
	global $db;
$getuser = mysqli_query($db, "select * from bit_members where email='".$_SESSION['email']."'");
$getem = mysqli_fetch_array($getuser);
$name = $getem['name'];
$_SESSION['user'] = $name;
$verified = $getem['verified'];
if ($verified != "YES"){
echo "<script>alert('Verify your mail first')</script>";
echo "<script>window.open('verify.php','_self')</script>";
}
$email = $_SESSION['email'];
$t = "buy";
$t2 = "sell";
$run = mysqli_query($db, "select * from bit_trans where user='$email' and type='$t'");
$run2 = mysqli_query($db, "select * from bit_trans where user='$email' and type='$t2'");
$suc = "success";
$run_tran = mysqli_query($db, "select * from bit_trans where user='$email' AND status='$suc'");


$count_buys = mysqli_num_rows($run);
$count_sell = mysqli_num_rows($run2);

$count_success = mysqli_num_rows($run_tran);



$run = mysqli_query($db, "select * from settings");
$row = mysqli_fetch_array($run);
$buyPM = $row['buyPM'];
$sellPM = $row['sellPM'];
//$buyBC = $row['buyBC'];
//$sellBC = $row['sellBC'];
$ratesellBC = $row['ratesellBC'];
$ratesellPM = $row['ratesellPM'];
$ratebuyBC = $row['ratebuyBC'];
$ratebuyPM = $row['ratebuyPM'];

$block = mysqli_query($db, "select * from bit_block where user='$email'");
$countblock = mysqli_num_rows($block);
if ($countblock > 0 ){
$rowblock = mysqli_fetch_array($block);
$reason = $rowblock['reason'];
echo "<div>
<h2>You have been blocked from making transactions for the following reasons<br>
$reason<br>
Please contact Admin via email to rectify your account.
</h2>
</div>
<a href='main.php'>Go Back</a>
"
;
exit;
	
}

$json  = file_get_contents("https://blockchain.info/ticker");
$json  =  json_decode($json ,true);
//echo "<br>";
//print_r($json);
$buyBC = $json["USD"]["buy"];
$sellBC = $json["USD"]["sell"];

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php seo(); ?>
    <title><?php webname(); ?> : Buy</title>
	<link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
    <link href="assets/css/custom.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    
<script language="javascript">
function BCconverter(){
var money = document.converter.money.value;
if (money == 1) {
document.converter.ng.value = document.converter.bc.value * <?php echo $buyBC;?> * <?php echo $ratebuyBC;?>;
}
if (money == 2) {
document.converter.ng.value = document.converter.bc.value * <?php echo $buyPM;?> * <?php echo $ratebuyPM;?>;
}

}

function ngconverter(){
var money = document.converter.money.value;
if (money == 1) {
document.converter.bc.value = document.converter.ng.value / (<?php echo $buyBC;?> * <?php echo $ratebuyBC;?>);
}
if (money == 2) {
document.converter.bc.value = document.converter.ng.value / (<?php echo $buyPM;?> *<?php echo $ratebuyPM;?>);
}

}

function moneychange(){
BCconverter();	
}

</script>

</head>
<body>
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" style="background-color:#006" href="index.php"><?php webname(); ?></a> 
            </div>
  <div style="color: white;
padding: 15px 50px 5px 50px;
float: right;
font-size: 16px;"> <a href="ref.php">Referal Bonus: <?php refamount(); ?></a> || <?php echo $_SESSION['user']; ?> &nbsp; <a href="logout.php" class="btn square-btn-adjust" style="background-color:#006">Logout</a> </div>
        </nav>   
           <!-- /. NAV TOP  -->
                <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
					<li class="text-center">
                    <img src="2.png" class="user-image img-responsive"/>
					</li>
				
					
                    <li>
                        <a href="main.php"><i class="fa fa-dashboard fa-3x"></i> Dashboard</a>
                    </li>
                    <li>
                        <a style="background-color:#006" href="buy.php"><i class="fa fa-bar-chart-o fa-3x"></i>Buy</a>
                    </li>	
                     <li>
                        <a  href="sell.php"><i class="fa fa-laptop fa-3x"></i>Sell</a>
                    </li>	
					<li>
                        <a  href="trans.php"><i class="fa fa-table fa-3x"></i>Transactions</a>
                    </li>
                    <li>
                        <a  href="settings.php"><i class="fa fa-edit fa-3x"></i> Settings</a>
                    </li>				
				</ul>
               
            </div>
            
        </nav>  
<!-- /. NAV SIDE  -->
<div id="page-wrapper" >
<div id="page-inner">
<div class="row">
<div class="col-md-12">
              	<h3 class="page-header">Please fill all forms to complete transaction</h3>
      		<form method="post" name="converter">
                <div class="form-group">
                Type
                <div class="col-lg-4">
            <select class="form-control" id="money" onChange="moneychange()">
                <option>--Select--</option>
            <option value=1>BTC</option>
  <!--          <option value=2>PM</option> -->
            </select>
                </div>
                </div>
                
                
                <div class="form-group">
                Amount
                <div class="col-lg-4">
                <input class="form-control" type="text" id="bc" name="bc" onKeyUp="BCconverter()"  required>
                </div>
                </div>
                <div class="form-group">
                Price in Naira
                <div class="col-lg-4">
                <input class="form-control" type="text" id="ng" name="ng" onKeyUp="ngconverter()" required>
                </div>
                </div>
                <div class="form-group">
                <div class="col-lg-4">
                <input class="btn btn-success" type="submit" name="tran1" value="Ok!">
                </div>
                </div>
      </form>
</div>
</div>              
</div>
</div>
<!-- /. WRAPPER  -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.metisMenu.js"></script>
    <script src="assets/js/morris/raphael-2.1.0.min.js"></script>
    <script src="assets/js/morris/morris.js"></script>
    <script src="assets/js/custom.js"></script>
    
   
</body>
</html>
 <?php } ?>
 
<?php
if (isset($_POST['tran1'])){

	$amount = $_POST['bc'];
	$price = $_POST['ng'];
	$user = $_SESSION['email'];
	$trackid = round(microtime(true)).strtoupper(substr($user,0,3));
	$type = "buy";
	$track1 = mysqli_query($db, "insert into bit_trans (user, amount, price, datesent, trackid, type) values ('$user','$amount','$price',NOW(),'$trackid','$type')");
	$_SESSION['trackid'] = $trackid;

	if ($track1){
	echo "<script>alert('Successful')</script>";
	echo "<script>window.open('buy2.php','_self')</script>";	
		}else{
	echo "<script>alert('Error, Try Again')</script>";	
	}
	} 
?>