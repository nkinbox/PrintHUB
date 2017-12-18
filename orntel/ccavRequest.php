<?php
$bname = basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH), ".php");
if($bname != "buy") {
header('Location: http://printhub.orntel.com/buy.php');
exit;
}
include '../library/functions.php';
$var=new session;
if($var->check_sessionid())
$userid=$var->userid;
else {
setcookie('ref','buy',0,'/','orntel.com',false,true);
setcookie('_error','2',time()+3,'/','orntel.com',false,true);
header('Location: http://my.orntel.com/login.php');
exit;
}
if(empty($_GET['buy']) && !in_array($_GET['buy'], array(1,2,3,4))) {
header('Location: http://printhub.orntel.com/buy.php');
exit;
}
$file1=fopen("orderid.txt","r");
$orderid=intval(fread($file1,10));
fclose($file1);
$orderid=$orderid + mt_rand(1,9);
$amount="0";
switch($_GET['buy']) {
case 1:
$amount="10.00";
break;
case 2:
$amount="20.00";
break;
case 3:
$amount="40.00";
break;
case 4:
$amount="60.00";
}
$file2=fopen("orderid.txt","w");
fwrite($file2,$orderid);
fclose($file2);
$var=new ccavenue($userid,$orderid,$amount);
$var->request();
?>
<!DOCTYPE html>
<html>
<head><title>Processing...</title></head>
<body>
<h2 style='text-align: center'>Please do not refresh or reload this page.</h2>
<div style='display: none'>
<form method="post" name="redirect" action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction"> 
<input type="hidden" name="encRequest" value="<?php echo $var->encrypted_data; ?>" />
<input type="hidden" name="access_code" value="<?php echo $var->access_code; ?>" />
</form>
</div>
<script>document.redirect.submit();</script>
</body>
</html>