<?php
if(empty($_COOKIE['_st']))
setcookie('_st','1',0,'/','.orntel.com',false,true);
include 'header.php';
if(empty($_COOKIE['err']))
$err=0;
else
$err=$_COOKIE['err'];
if(empty($_COOKIE['_st']))
$step=1;
else {
switch($_COOKIE['_st'])
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
case 3:
if($bn == 3) echo " autofocus ";
break;
case 4:
if($bn == 4) echo " autofocus ";
break;
case 5:
if($bn == 5) echo " autofocus ";
break;
case 6:
if($bn == 6) echo " autofocus ";
break;
default:
if($bn == 3) echo " autofocus ";
}
}
}
function serr() {
switch($GLOBALS['err']) {
case 1:
echo "<p>Phone number is Invalid.</p>";
break;
case 2:
echo "<p>Incorrect verification Code.</p>";
break;
case 3:
echo "<p>Name seems to be incorrect. Enter full name.</p>";
break;
case 4:
echo "<p>Username is either invalid or already taken or too short.</p>";
break;
case 5:
echo "<p>Password seems to be weak.</p>";
break;
case 6:
echo "<p>Confirm password again.</p>";
break;
case 7:
echo "<p>This number has been marked SPAM. Try another number.</p>";
break;
case 8:
echo "<p>Verification code has been requested too many times. Try after some time.</p>";
break;
case 9:
echo "<p>An Error occured while Adding new user. Try again.</p>";
break;
case 10:
echo "<p>This number is already registerd.</p>";
break;
default:
echo "";
}
}
?>
<h2>Create your Orntel account</h2>
<section>
<?php serr();?>
<div class='g'><div class='c<?php fstep(1,1);?>' id='c1'>
<div class='s'><div>1</div></div>
<form name='s1' action='v.php' method='post'<?php fstep(1,2);?>>
<input type='text' name='phone' placeholder='Phone number' style='background-position: 0 -256px'<?php fstep(1,4);?>/>
</form>
<div class='bt nx'<?php fstep(1,3);?>>Next</div>
<br/>
<div class='inc'><div class='in'>Do not prefix +91. <br>This Number will be used for notification purpose only.</div></div>
<div class='inc'><div id='sp'>We do not spam</div></div>
</div></div><div class='g'><div class='c<?php fstep(2,1);?>' id='c2'>
<div class='s'><div>2</div></div>
<form name='s2' action='v.php' method='post'<?php fstep(2,2);?>>
<input type='text' name='verify' placeholder='verification code' style='background-position: 0 -192px'<?php fstep(2,4);?>/>
</form>
<form name='s4' action='v.php' method='post'<?php fstep(2,2);?>>
<input type='hidden' name='reset' value='1'/>
</form><div class='bt' style='background-position: 0 -32px'<?php mstep(2);?>>Back</div>
<div class='bt' style='background-position: 0 -98px; float:right'<?php fstep(2,3);?>>Verify</div>
<br/>
<div class='inc'><div class='in'>A verification code will be received on Phone number entered in previous step.</div></div>
</div></div><div class='g'><div class='c<?php fstep(3,1);?>' id='c3'>
<div class='s'><div>3</div></div>
<form name='s3' action='v.php' method='post'<?php fstep(3,2);?>>
<input type='text' name='name' placeholder='Full Name' <?php if(!empty($_COOKIE['_na'])) echo "value='" .$_COOKIE['_na']. "' ";?>style='background-position: 0 -288px'<?php mstep(3,3);?>/>
<input type='text' name='username' placeholder='Choose username' <?php if(!empty($_COOKIE['_un'])) echo "value='" .$_COOKIE['_un']. "' ";?>style='background-position: 0 -224px'<?php mstep(3,4);?>/>
<input type='password' name='password' placeholder='New password' style='background-position: 0 -160px'<?php mstep(3,5);?>/>
<input type='password' name='cpassword' placeholder='Confirm password' style='background-position: 0 -160px'<?php mstep(3,6);?>/>
</form>
<div class='bt' style='background-position: 0 0; float:right'<?php fstep(3,3);?>>Finish</div>
<br/>
<div class='inc'><div class='in'>Name will be used only for identification purpose. By clicking &quot;Finish&quot; button you agree to all <a href='terms.php' target='_blank'>orntel policy</a></div></div>
</div></div>
<div id='faq_'>
<a href='http://support.orntel.com'><div id='faq'>Frequently Asked Questions</div></a>
<a class='qst' href='http://support.orntel.com/question/Are-my-details-secure-on-Orntel.php'>Are my details secure on Orntel?</a>
<a class='qst' href='http://support.orntel.com/question/Why-should-i-sign-up-on-Orntel.php'>Why should i sign up on Orntel?</a>
<a class='qst' href='http://support.orntel.com/question/My-number-has-been-blocked-on-Orntel-What-should-i-do-now.php'>My number has been blocked on Orntel. What should i do now?</a>
</div>
</section>
<?php include 'footer.php';?>