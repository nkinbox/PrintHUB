<?php include 'header.php';
if(empty($_GET['show']))
$show="my_predefined";
else
$show=$_GET['show'];
switch($show) {
case "my_predefined":
$t=1;
break;
case "history":
$t=2;
break;
case "credits":
$t=3;
break;
case "mail":
$t=4;
break;
default:
$t=1;
}
$err=0;
if(!empty($_COOKIE['maerr']))
$err=$_COOKIE['maerr'];
function showerr() {
switch($GLOBALS['err']) {
case 1:
echo "<div id='erc'>Invalid Print request.</div>";
break;
case 2:
echo "<div id='ercc'>Reprint request send successfully.</div>";
break;
case 3:
echo "<div id='erc'>Reprint request is already in process.</div>";
break;
case 4:
echo "<div id='erc'>Incomplete Request parameters. Please Retry.</div>";
break;
case 5:
echo "<div id='ercc'>Print Task successfully restarted.</div>";
break;
default:
echo "";
}}
?>
<section>
<?php showerr(); ?>
<div id='m_con'>
<div id='l_con'>
<div class='_link<?php if($t==1) echo " se_l";?>'><a id='_my' href='account.php?show=my_predefined'><span class='tab'>My predefined</span></a></div>
<div class='_link<?php if($t==2) echo " se_l";?>'><a id='_hi' href='account.php?show=history'><span class='tab'>Printout history</span></a></div>
<div class='_link<?php if($t==3) echo " se_l";?>'><a id='_cr' href='account.php?show=credits'><span class='tab'>Credits</span></a></div>
<div class='_link<?php if($t==4) echo " se_l";?>'><?php countmymails(); ?><a id='_ma' href='account.php?show=mail'><span class='tab'>Mail box</span></a></div>
<div class='_link'><a id='_ac' href='http://my.orntel.com/account.php'><span class='tab'>Account settings</span></a></div>
</div>
<div id='_content'>
<?php if($t == 1) {
echo "<div id='fb-root'></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = '//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0';
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>";
mypredefined();
} elseif($t == 2) {
myhistory();
} elseif($t == 3) {
mycredits();
} elseif($t == 4) {
mymails();
}?>
</div>
</div>
<div id='faq_'>
<a href='http://support.orntel.com'><div id='faq'>Frequently Asked Questions</div></a>
<a class='qst' href='http://support.orntel.com/question/Why-should-i-set-Alternate-Number-in-account-setting.php'>Why should i set `Alternate Number` in account setting?</a>
<a class='qst' href='http://support.orntel.com/question/Why-should-i-set-Security-Question.php'>Why should i set `Security Question`?</a>
<a class='qst' href='http://support.orntel.com/question/Why-reprint-option-is-not-Visible-in-my-Print-History.php'>Why reprint option is not Visible in my Print History?</a>
</div>
</section>
<script src='http://static.orntel.com/j/p_m.js'></script>
<?php include 'footer.php';?>