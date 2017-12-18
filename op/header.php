<?php
include '../library/functions.php';
$var=new session;
if($var->check_sessionid()) {
$login=true;
$userid=$var->userid;
}
else
$login=false;
unset($var);
$tab=basename($_SERVER['PHP_SELF'],".php");
$t=array(false,false,false,false,false,false,false,false);
$sheet="";
$script="";
switch($tab)
{
case "index":
$t[0]=true;
$sheet= '<link rel="stylesheet" href="http://static.orntel.com/s/p_i.css">';
break;
case "stats":
$t[1]=true;
$sheet= '<link rel="stylesheet" href="http://static.orntel.com/s/p_s.css">
<script src="http://static.orntel.com/j/c.js"></script>';
break;
case "team":
$t[2]=true;
$sheet= '<link rel="stylesheet" href="http://static.orntel.com/s/p_t.css">';
break;
case "contact":
$t[3]=true;
$sheet= '<link rel="stylesheet" href="http://static.orntel.com/s/p_c.css">';
break;
case "buy":
$t[4]=true;
$sheet= '<link rel="stylesheet" href="http://static.orntel.com/s/p_b.css">';
break;
case "about":
$t[5]=true;
$sheet= '<link rel="stylesheet" href="http://static.orntel.com/s/p_a.css">';
break;
case "help":
$t[6]=true;
break;
case "print":
if(!$login) {
setcookie('ref',$tab,0,'/','.orntel.com',false,true);
setcookie('_error','2',time()+3,'/','.orntel.com',false,true);
header('Location: http://my.orntel.com/login.php');
exit;
}
$sheet= '<link rel="stylesheet" href="http://static.orntel.com/s/p_print.css">';
$script= '<script src="http://static.orntel.com/j/p.js"></script>';
break;
case "predefined":
if(!$login) {
setcookie('ref',$tab,0,'/','.orntel.com',false,true);
setcookie('_error','2',time()+3,'/','.orntel.com',false,true);
header('Location: http://my.orntel.com/login.php');
exit;
}
$sheet= '<link rel="stylesheet" href="http://static.orntel.com/s/p_p.css">';
$script= '<script src="http://static.orntel.com/j/p_p.js"></script>';
break;
case "predefined_list":
$sheet= '<link rel="stylesheet" href="http://static.orntel.com/s/p_l.css">';
break;
case "predefined_share":
$sheet= '<meta property="og:site_name" content="Orntel Printhub" />
<meta property="og:title" content="Link to Printer Web Interface" />
<meta property="og:description" content="Now give Print command from any device to Orntel printer located near you. And collect printouts at you convenience." />
<meta property="og:image" content="http://static.orntel.com/i/fb_link_share.png" />
';
break;
case "account":
if(!$login) {
setcookie('ref',$tab,0,'/','.orntel.com',false,true);
setcookie('_error','2',time()+3,'/','.orntel.com',false,true);
header('Location: http://my.orntel.com/login.php');
exit;
}
$t[7]=true;
$sheet= '<link rel="stylesheet" href="http://static.orntel.com/s/p_m.css">';
}
unset($tab);
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<title>Printhub - Online printing solution</title>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1">
<meta name="Keywords" content="printhub, orntel, online printing, print online, orntel printhub, printhub orntel, online print, printhub predefined, print">
<meta name="Description" content="Orntel printhub is an online printing solution. You can access our printers using web-interface and print PDF files from any device with browser.">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="http://static.orntel.com/s/p_h.css">
<?php echo $sheet;?>
</head>
<body>
<div id='c_'>
<header>
<div id='l'><a href='index.php'><img src='http://static.orntel.com/i/p.png' alt='printhub' style='width:153px; height:30px'/></a>
<a id='help' class='_n<?php if($t[6]) echo " fsl";?>' href='http://support.orntel.com/index.php'>HELP</a>
</div>
<nav>
<div class='m_'><a href='#' onclick='sh(); return false;' id='_u'></a></div>
<div class='h'<?php if($t[0]) echo " id='sl'";?>><a href='index.php' id='_h'>Home</a></div>
<div class='h'<?php if($t[1]) echo " id='sl'";?>><a href='stats.php' id='_s'>Stats</a></div>
<div class='h'<?php if($t[2]) echo " id='sl'";?>><a href='team.php' id='_t'>Team</a></div>
<div class='h'<?php if($t[3]) echo " id='sl'";?>><a href='contact.php' id='_c'>Contact</a></div>
<div class='h'<?php if($t[4]) echo " id='sl'";?>><a href='buy.php' id='_b'>Buy credits</a></div>
<div class='h'<?php if($t[5]) echo " id='sl'";?>><a href='about.php' id='_a'>About</a></div>
<?php if($login) {?>
<div class='fr'><a href='http://my.orntel.com/logout.php' id='_o'>Logout</a></div>
<div class='fr'<?php if($t[7]) echo " id='sl'";?>><a href='account.php' id='_m'>Account</a></div>
<?php } else {?>
<div class='fr'><a href='http://my.orntel.com/signup.php' id='_r'>Sign up</a></div>
<div class='fr'><a href='http://my.orntel.com/login.php' id='_i'>Login</a></div>
<?php } unset($t);?>
<br style='clear:both' />
</nav>
</header>
<div class='moc'><a class='mo' href='print.php'>Print custom</a><a class='mo' href='predefined_list.php'>Print Predefined</a></div>
