<?php
include 'header.php';?>
<h2>Login to your Orntel account</h2>
<section>
<?php
if(empty($_COOKIE['_error']))
$err=0;
else
$err=$_COOKIE['_error'];
switch($err) {
case 1:
echo "<p>Invalid username or password.</p>";
break;
case 2:
echo "<p>Please login to your account.</p>";
break;
case 3:
echo "<p>Due to illegal access attempts your account has been blocked for " .$_COOKIE['_da']. " sec.</p>";
break;
case 4:
echo "<p>You deactivated this account. Login to reactivate.</p>";
break;
case 5:
echo "<p>Your account is active from " .$_COOKIE['_da']. " locations. Login again to discard session.</p>";
break;
default:
echo "";
}
if(!empty($_GET['success']) && $_GET['success'] == 1)
echo "<p id='green'>Success! Please login to your account</p>";
?>
<form action='v.php' method='post' name='lf'>
<?php
if(!empty($_GET['success']) && $_GET['success'] == 1)
echo "<input type='hidden' name='r' value='1' />";
?>
<input tabindex='1' type='text' name='u' placeholder='Username' <?php if(!empty($_COOKIE['un'])) echo "value='" .$_COOKIE['un']. "' ";?>style='background-position: 0 0' autofocus />
<input tabindex='2' type='password' name='p' placeholder='Password' style='background-position: 0 -32px' />
<input type='submit' style='display:none'>
<div id='c'><div tabindex='3' id='s' onclick='lf.submit()'>Submit</div><a tabindex='4' id='res' href='forgot.php'></a></div>
</form>
<div id='nsu'><a tabindex='5' href='signup.php'>Create new account</a></div>
<div id='faq_'>
<a href='http://support.orntel.com'><div id='faq'>Frequently Asked Questions</div></a>
<a class='qst' href='http://support.orntel.com/question/I-forgot-My-Orntel-account-password-How-to-recover-my-account.php'>I forgot My Orntel account password. How to recover my account?</a>
<a class='qst' href='http://support.orntel.com/question/I-forgot-My-Orntel-account-Username-How-to-recover-my-account.php'>I forgot My Orntel account Username. How to recover my account?</a>
<a class='qst' href='http://support.orntel.com/question/I-am-getting-error-Your-account-is-active-from-2-locations-Login-again-to-discard-session.php'>I am getting error `Your account is active from 2 locations. Login again to discard session`.</a>
</div>
</section>
<?php include 'footer.php';?>