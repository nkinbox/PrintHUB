<?php include 'header.php';
$err=0;
if(!empty($_COOKIE['eru']))
$err=$_COOKIE['eru'];
function serror() {
switch($GLOBALS['err']) {
case 1:
echo "<div id='erc'>Invalid Data combination. Please try again.</div>";
break;
case 2:
echo "<div id='ercc'>SMS containing your username has been sent to you. <a href='login.php'>click here to login.</a></div>";
break;
default:
echo "";
}}
?>
<section>
<?php serror(); ?>
<p>Recover Username of Orntel account</p>
<form name='f1' action='v.php' method='post'>
<label>what is your secret word that you added in account setting?</label>
<div class='ad'></div>
<input type='text' name='sw' placeholder='Secret Word' />
<label>Your Registered Primary or Alternate Phone number.<br />Do not Prefix +91</label>
<div class='ad'></div>
<input type='text' name='ph' placeholder='Phone number' />
</form>
<div id='btn' onclick='f1.submit()'>Get Username</div>
</section>
<?php include 'footer.php';?>