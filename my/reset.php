<?php
if(empty($_COOKIE['_rst']))
setcookie('_rst','1',0,'/','.orntel.com',false,true);

include 'header.php';
if(empty($_COOKIE['rerr']))
$err=0;
else
$err=$_COOKIE['rerr'];
if(empty($_COOKIE['_rst']))
$step=1;
else {
switch($_COOKIE['_rst'])
{
case 1:
$step=1;
break;
case 2:
$step=2;
break;
case 3:
$step=3;
break;
default:
$step=1;
}
}
function fstep($col, $d) {
$step=$GLOBALS['step'];
switch($d) {
case 1:
if($col != $step) { echo " op";}
break;
case 2:
if($col != $step) { echo " onsubmit='return false' ";}
break;
case 3:
if($col == $step) { echo " onclick='s" .$col. ".submit()'";}
break;
case 4:
if($col == $step) { echo " autofocus ";}
}
}

function mstep($d,$bn=0) {
$step=$GLOBALS['step'];
if($d == 2 && $step == 2)
echo " onclick='s4.submit()' ";
if($d == 3 && $step == 3) {
switch($GLOBALS['err']) {
case 5:
if($bn == 5) echo " autofocus ";
break;
case 6:
if($bn == 6) echo " autofocus ";
break;
default:
if($bn == 5) echo " autofocus ";
}
}
}
function serr() {
switch($GLOBALS['err']) {
case 1:
echo "<p>Phone number is invalid or not registerd.</p>";
break;
case 2:
echo "<p>Incorrect verification Code.</p>";
break;
case 3:
echo "<p>Verification code has been requested too many times. Try after some time.</p>";
break;
case 4:
echo "<p>Account related to this number seems to be blocked or inactive.</p>";
break;
case 5:
echo "<p>Password seems to be weak.</p>";
break;
case 6:
echo "<p>Confirm password again.</p>";
break;
case 7:
echo "<p>An error occured while changing password. Try again.</p>";
break;default:
echo "";
}
}
?>
<h2>Reset Orntel account password</h2>
<section>
<?php serr();?>
<div class='g'><div class='c<?php fstep(1,1);?>' id='c1'>
<div class='s'><div>1</div></div>
<form name='s1' action='v.php' method='post'<?php fstep(1,2);?>>
<input type='text' name='phone' placeholder='Phone number' style='background-position: 0 -256px'<?php fstep(1,4);?>/>
</form>
<div class='bt nx'<?php fstep(1,3);?>>Next</div>
<br/>
<div class='inc'><div class='in'>Do not prefix +91.<br/>Enter phone number provided during registration.<br/>Or alternate number you added for recovery.</div></div>
</div></div><div class='g'><div class='c<?php fstep(2,1);?>' id='c2'>
<div class='s'><div>2</div></div>
<form name='s2' action='v.php' method='post'<?php fstep(2,2);?>>
<input type='text' name='verify' placeholder='verification code' style='background-position: 0 -192px'<?php fstep(2,4);?>/>
</form><form name='s4' action='v.php' method='post'<?php fstep(2,2);?>>
<input type='hidden' name='reset' value='1'/>
</form><div class='bt' style='background-position: 0 -32px'<?php mstep(2);?>>Back</div>
<div class='bt' style='background-position: 0 -98px; float:right'<?php fstep(2,3);?>>Verify</div>
<br/>
<div class='inc'><div class='in'>A verification code will be received on Phone number entered in previous step.</div></div>
</div></div><div class='g'><div class='c<?php fstep(3,1);?>' id='c3'>
<div class='s'><div>3</div></div>
<form name='s3' action='v.php' method='post'<?php fstep(3,2);?>>
<input type='password' name='password' placeholder='New password' style='background-position: 0 -160px'<?php mstep(3,5);?>/>
<input type='password' name='cpassword' placeholder='Confirm password' style='background-position: 0 -160px'<?php mstep(3,6);?>/>
</form>
<div class='bt' style='background-position: 0 0; float:right'<?php fstep(3,3);?>>Finish</div>
<br/>
<div class='inc'><div class='in'>Choose new password</div></div>
</div></div>
</section>
<?php include 'footer.php';?>