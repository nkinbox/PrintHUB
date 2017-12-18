<?php
if(!(!empty($_GET['id']) && strlen($_GET['id']) == 32)) {
header('Location: http://my.orntel.com/login.php');
exit;
}
include '../library/dbconnect.php';
$con=new dbconnect("db_credentials");
$con->q_exe("select login from session where (sessionid = :sessionid and logout = 0) limit 1;",array("sessionid"=>$_GET['id']));
$rows=$con->rows;
unset($con);
if(count($rows) == 0) {
header('Location: http://my.orntel.com/login.php');
exit;
}
if($rows[0]['login']+10 < time()) {
header('Location: http://my.orntel.com/login.php');
exit;
}
setcookie('_id',$_GET['id'],0,'/','',false,true);

if(empty($_COOKIE['ref']))
header('Location: http://printhub.orntel.com/account.php');
else {
$ref=$_COOKIE['ref'];
switch($ref) {
case "oaccount":
$ref="account.php";
break;
case "account":
$ref="http://printhub.orntel.com/account.php";
break;
case "print":
$ref="http://printhub.orntel.com/print.php";
break;
case "predefined":
$ref="http://printhub.orntel.com/predefined.php";
break;
case "buy":
$ref="http://printhub.orntel.com/buy.php";
break;
default:
$ref="http://printhub.orntel.com/account.php";
}
setcookie('ref','0',time()-3600,'/','.orntel.com',false,true);
header('Location: ' . $ref);
}
exit;
?>