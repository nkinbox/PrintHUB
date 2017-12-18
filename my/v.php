<?php
if(empty($_SERVER['HTTP_REFERER']))
exit('[Invalid access]');
include '../library/functions.php';
if(empty($_POST)) {
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit; }
$bname = basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH), ".php");

if($bname == "logout") {
if($_POST['logout'] == 1) {
$var= new session;
$var->logout();
if(empty($_COOKIE['ref']))
header('Location: index.php');
else {
$ref=$_COOKIE['ref'] . ".php";
setcookie('ref','0',time()-3600,'/','.orntel.com',false,true);
header('Location: ' . $ref);
}}
else {
setcookie('ref','0',time()-3600,'/','.orntel.com',false,true);
header('Location: index.php');
}
exit;
} elseif($bname == "account") {
if(!empty($_POST['edit'])) {
$var=new session;
if(!$var->check_sessionid()) {
setcookie('ref','oaccount',0,'/','.orntel.com',false,true);
header('Location: login.php');
exit;
}
$userid=$var->userid;
switch($_POST['edit']) {
case 1:
$var=new adv_setting;
$var->userid=$userid;
$var->editdata();
if(!empty($var->errorid)) {
$co="";
foreach($var->errorid as $id) {
if($co == "")
$co=$id;
else
$co.="|" .$id;
}
setcookie('aerr',$co,time()+3,'/','.orntel.com',false,true);
}
if($_POST['active'] == 'd') {
setcookie("_id","0",time()-7200,'/','.orntel.com',false,true);
header('Location: index.php');
exit;
}
break;
case 2:
$var=new personal;
$var->userid=$userid;
$var->editdata();
if(!empty($var->errorid)) {
$co="";
foreach($var->errorid as $id) {
if($co == "")
$co=$id;
else
$co.="|" .$id;
}
setcookie('aerr',$co,time()+3,'/','.orntel.com',false,true);
}
break;
case 3:
$var=new prepayment;
$var->userid=$userid;
$var->editdata();
if(!empty($var->errorid)) {
$co="";
foreach($var->errorid as $id) {
if($co == "")
$co=$id;
else
$co.="|" .$id;
}
setcookie('aerr',$co,time()+3,'/','.orntel.com',false,true);
}
break;
}
}
header('Location: account.php');
exit;
} else {
$var=new session;
if($var->check_sessionid()) {
header('Location: logout.php');
exit;
}
unset($var);
if($bname == "login") {
$var=new session;
$var->set_sessionid();
if($var->errorid == 0) {
if(!empty($_POST['r']) && $_POST['r'] == 1) {
$var2=new mailbox($var->userid,1);
$var2->subject="Set Recovery Options";
$var2->content="Please set Alternate phone number & Secret word in <a href='http://my.orntel.com/account.php'>Account setting</a> Tab. This will help in fast account recovery.";
$var2->send();
header('Location: http://printhub.orntel.com/account.php');
} elseif(empty($_COOKIE['ref']))
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
}
$var->cookie("error",$var->errorid);
setcookie('_error',$var->errorid,time()+3,'/','.orntel.com',false,true);
header('Location: login.php');
exit;
} elseif($bname == "signup") {
$var=new account;
if(!empty($_POST['reset']) && $_POST['reset'] == 1) {
$var->reset();
header('Location: signup.php');
exit;
}
$var->cookie('err','0');
switch($_COOKIE['_st']) {
case 1:
$var->set_phone();
if($var->errorid == 0) {
$var->cookie('_st','2');
} else $var->cookie('err',$var->errorid);
break;
case 2:
$var->set_phone();
$var->set_code();
if($var->errorid == 0) {
$var->cookie('_st','3');
} else $var->cookie('err',$var->errorid);
break;
case 3:
$var->set_name();
if($var->errorid != 0) {
$var->cookie('err',$var->errorid);
header('Location: signup.php');
exit;
}
$var->set_username();
if($var->errorid != 0) {
$var->cookie('err',$var->errorid);
header('Location: signup.php');
exit;
}
$var->set_password();
if($var->errorid != 0) {
$var->cookie('err',$var->errorid);
header('Location: signup.php');
exit;
}
$var->set_phone();
$var->set_code();
if($var->errorid == 0) {
$var->add_user();
header('Location: login.php?success=1');
exit;
} elseif($var->errorid == 9) {
$var->reset();
$var->cookie('err',$var->errorid);
} else $var->cookie('err',$var->errorid);
}
header('Location: signup.php');
exit;
} elseif($bname == "username") {
if(empty($_COOKIE['uat'])) {
setcookie("uat","1",0,'/','.orntel.com',false,true);
$e=1;
} else {
$e=intval($_COOKIE['uat']);
$e=$e+1;
setcookie("uat",$e,0,'/','.orntel.com',false,true);
}
if($e > 3) {
header('Location: index.php');
exit;
}
if(!empty($_POST['sw']) && !empty($_POST['ph']) && strlen($_POST['ph'])==10 && preg_match('/^[0-9]+$/i', $_POST['ph'])) {
$var=new reset_u;
$var->answer=substr(md5(sha1($_POST['sw'])),0,10);
$var->phone="+91" . $_POST['ph'];
if($var->recover_u())
setcookie("eru","2",time()+3,'/','.orntel.com',false,true);
else
setcookie("eru","1",time()+3,'/','.orntel.com',false,true);
}
else
setcookie("eru","1",time()+3,'/','.orntel.com',false,true);
header('Location: username.php');
exit;
} elseif($bname == "reset") {
$var=new reset_p;
if(!empty($_POST['reset']) && $_POST['reset'] == 1) {
$var->reset();
header('Location: reset.php');
exit;
}
$var->cookie('rerr','0');
switch($_COOKIE['_rst']) {
case 1:
$var->set_phone();
if($var->errorid == 0) {
$var->cookie('_rst','2');
} else $var->cookie('rerr',$var->errorid);
break;
case 2:
$var->set_phone();
$var->set_code();
if($var->errorid == 0) {
$var->cookie('_rst','3');
} else $var->cookie('rerr',$var->errorid);
break;
case 3:
$var->set_password();
if($var->errorid != 0) {
$var->cookie('rerr',$var->errorid);
header('Location: reset.php');
exit;
}
$var->set_phone();
$var->set_code();
if($var->errorid == 0) {
$var->reset_password();
header('Location: login.php?success=1');
exit;
} elseif($var->errorid == 7) {
$var->reset();
$var->cookie('rerr',$var->errorid);
} else $var->cookie('rerr',$var->errorid);
}
header('Location: reset.php');
exit;
}
else {
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
}
}
?>