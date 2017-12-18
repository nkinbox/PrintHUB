<?php
include '../library/functions.php';
$tab=basename($_SERVER['PHP_SELF'],".php");
switch($tab) {
case 'login':
case 'signup':
case 'reset':
case 'username':
case 'forgot':
$divert=true;
break;
default:
$divert=false;
}
$var=new session;
$logged=false;
if($var->check_sessionid()) {
$userid=$var->userid;
$logged=true;
if($divert) {
setcookie('ref',$tab,0,'/','.orntel.com',false,true);
header('Location: logout.php');
exit;
}}
if($tab=="account" && !$logged) {
setcookie('ref',"oaccount",0,'/','.orntel.com',false,true);
header('Location: login.php');
exit;
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<title>Orntel</title>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1">
<meta name="Keywords" content="orntel, orntel login, orntel account, orntel signup, orntel policy">
<meta name="Description" content="Create and manage Orntel account">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="http://static.orntel.com/s/o_h.css">
<?php
if($tab == "login")
echo '<link rel="stylesheet" href="http://static.orntel.com/s/o_l.css">';
elseif($tab == "signup" || $tab == "reset")
echo '<link rel="stylesheet" href="http://static.orntel.com/s/o_s.css">';
elseif($tab == "logout")
echo '<link rel="stylesheet" href="http://static.orntel.com/s/o_e.css">';
elseif($tab == "account")
echo '<link rel="stylesheet" href="http://static.orntel.com/s/o_m.css">';
elseif($tab == "terms")
echo '<link rel="stylesheet" href="http://static.orntel.com/s/o_t.css">';
elseif($tab == "forgot")
echo '<link rel="stylesheet" href="http://static.orntel.com/s/o_f.css">';
elseif($tab == "username")
echo '<link rel="stylesheet" href="http://static.orntel.com/s/o_u.css">';
?>
</head>
<body>
<header>
<a href='index.php'><img src='http://static.orntel.com/i/o.png' alt='orntel' /></a>
</header>