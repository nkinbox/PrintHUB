<?php include 'header.php';
function ser($e) {
switch($e) {
case 1:
echo "<div class='aerr'>Name was not changed.</div>";
break;
case 2:
echo "<div class='aerr'>Birthday was not changed.</div>";
break;
case 3:
echo "<div class='aerr'>Email was not changed.</div>";
break;
case 4:
echo "<div class='aerr'>Phone number was not changed.</div>";
break;
case 5:
echo "<div class='aerr'>College was not changed.</div>";
break;
case 6:
echo "<div class='aerr'>Address was not changed.</div>";
break;
case 7:
echo "<div class='aerr'>City was not changed.</div>";
break;
case 8:
echo "<div class='aerr'>State was not changed.</div>";
break;
case 9:
echo "<div class='aerr'>Pin was not changed.</div>";
break;
case 10:
echo "<div class='aerr'>Country was not changed.</div>";
break;
case 11:
echo "<div class='aerr'>Alternate number was not changed.</div>";
break;
}}
?>
<div id='division'>
<a href='index.php'>Back</a>
</div>
<h2>My Account</h2>
<section>
<?php
if(!empty($_COOKIE['aerr'])) {
$err=explode("|",$_COOKIE['aerr']);
foreach($err as $e)
ser($e);
}
?>
<div class='con2' style='background-position: 0 -64px'>Account Setting<a href='account.php?edit=1'><div class='edit'>Edit</div></a></div>
<?php if(!empty($_GET['edit']) && $_GET['edit'] == 1) {
$var=new adv_setting;
$var->userid=$userid;
$var->extractdata();
?>
<form name='f1' method='post' action='v.php'>
<input type='hidden' name='edit' value='1' />
<div class='con3'>
<div class='ch1'>Alternate Number</div>
<div class='chc'><input type='text' name='altphone' placeholder='prefix +91, Alternate phone number' value='<?php echo $var->altphone; ?>' autofocus /></div>
<div class='ch1'>Security Question</div>
<div class='chc'><input type='text' name='answer' placeholder='Type your secret word' /></div>
</div><div class='con3'>
<div class='ch1'>Maximum Logins</div>
<div class='chc'><select style='margin: 1px' name='maxlogin'>
<option value='1'<?php if($var->maxlogin == 1) echo " selected"; ?>>1 Device</option>
<option value='2'<?php if($var->maxlogin == 2) echo " selected"; ?>>2 Devices</option>
<option value='3'<?php if($var->maxlogin == 3) echo " selected"; ?>>3 Devices</option>
<option value='4'<?php if($var->maxlogin == 4) echo " selected"; ?>>4 Devices</option>
<option value='5'<?php if($var->maxlogin == 5) echo " selected"; ?>>5 Devices</option>
</select></div>
<div class='ch1'>Session Duration</div>
<div class='chc'><select style='margin: 1px' name='maxduration'>
<option value='1'<?php if($var->maxduration == 1) echo " selected"; ?>>1 Hour</option>
<option value='2'<?php if($var->maxduration == 2) echo " selected"; ?>>2 Hours</option>
<option value='12'<?php if($var->maxduration == 12) echo " selected"; ?>>12 Hours</option>
<option value='24'<?php if($var->maxduration == 24) echo " selected"; ?>>24 Hours</option>
</select></div>
</div>
<div class='ch1'>Account Status</div>
<div class='chc'><select style='margin: 1px' name='active'>
<option value='a'>Active</option>
<option value='d'>Deactivate</option>
</select></div>
</form>
<div class='emo'>
<div class='yes' onclick='f1.submit()'>Save changes</div>&nbsp;<a href='account.php'><div class='no'>Cancel</div></a>
</div>
<?php } else {
$var=new adv_setting;
$var->userid=$userid;
$var->extractdata();
?>
<div class='con3'>
<div class='ch1'>Alternate Number</div>
<div class='chc'><?php echo $var->altphone; ?></div>
<div class='ch1'>Security Question</div>
<div class='chc'><?php echo $var->answer; ?></div>
</div><div class='con3'>
<div class='ch1'>Maximum Logins</div>
<div class='chc'><?php echo $var->maxlogin; ?> Devices</div>
<div class='ch1'>Session Duration</div>
<div class='chc'><?php echo $var->maxduration; ?> Hour(s)</div>
</div>
<div class='ch1'>Account Status</div>
<div class='chc'><?php if($var->active == 1) echo "Active"; ?></div>
<?php } ?>
<div class='con2' style='background-position: 0 0'>Personal Data<a href='account.php?edit=2'><div class='edit'>Edit</div></a></div>
<?php
$var = new personal;
$var->userid=$userid;
$var->extractdata();
if(!empty($_GET['edit']) && $_GET['edit'] == 2) {
?>
<form name='f2' method='post' action='v.php'>
<input type='hidden' name='edit' value='2' />
<div class='con3'>
<div class='ch1'>Name</div>
<div class='chc'><input type='text' name='name' placeholder='Full name' value='<?php echo $var->name; ?>' autofocus /></div>
<div class='ch1'>Sex</div>
<div class='chc'>
<select style='margin:1px' name='sex'>
<option value='M' <?php if($var->sex == 'M') echo "selected"; ?>>Male</option>
<option value='F' <?php if($var->sex != 'M') echo "selected"; ?>>Female</option>
</select>
</div>
<div class='ch1'>College</div>
<div class='chc'><input type='text' name='college' placeholder='college' value='<?php echo $var->college; ?>' /></div>
</div><div class='con3'>
<div class='ch1'>Birthday</div>
<div class='chc'><input type='text' name='bday' placeholder='dd MMM YY' value='<?php echo $var->bday; ?>' /></div>
<div class='ch1'>Email</div>
<div class='chc'><input type='text' name='email' placeolder='Email' value='<?php echo $var->email; ?>' /></div>
<div class='ch1'>Primary number</div>
<div class='chc'><input type='text' value='<?php echo $var->phone; ?>' disabled /></div>
</div>
</form>
<div class='emo'>
<div class='yes' onclick='f2.submit()'>Save changes</div>&nbsp;<a href='account.php'><div class='no'>Cancel</div></a>
</div>
<?php } else {?>
<div class='con3'>
<div class='ch1'>Name</div>
<div class='chc'><?php echo $var->name; ?></div>
<div class='ch1'>Sex</div>
<div class='chc'><?php if($var->sex == 'M') echo "Male"; else echo "Female"; ?></div>
<div class='ch1'>College</div>
<div class='chc'><?php echo $var->college; ?></div>
</div><div class='con3'>
<div class='ch1'>Birthday</div>
<div class='chc'><?php echo $var->bday; ?></div>
<div class='ch1'>Email</div>
<div class='chc'><?php echo $var->email; ?></div>
<div class='ch1'>Primary number</div>
<div class='chc'><?php echo $var->phone; ?></div>
</div>
<?php } ?>
<div class='con2' style='background-position: 0 -32px'>Pre-payment Data<a href='account.php?edit=3'><div class='edit'>Edit</div></a></div>
<?php
$var = new prepayment;
$var->userid=$userid;
$var->extractdata();
if(!empty($_GET['edit']) && $_GET['edit'] == 3) {
?>
<form name='f3' method='post' action='v.php'>
<input type='hidden' name='edit' value='3' />
<div class='con3'>
<div class='ch1'>Name</div>
<div class='chc'><input type='text' name='name' placeholder='Full name' value='<?php echo $var->name; ?>' autofocus /></div>
<div class='ch1'>Phone</div>
<div class='chc'><input type='text' name='phone' placeholder='Prefix +91, Phone number' value='<?php echo $var->phone; ?>' /></div>
<div class='ch1'>Email</div>
<div class='chc'><input type='text' name='email' placeholder='Email' value='<?php echo $var->email; ?>' /></div>
</div><div class='con3'>
<div class='ch1'>Country</div>
<div class='chc'><input type='text' name='country' placeholder='Country' value='<?php echo $var->country; ?>' /></div>
<div class='ch1'>City</div>
<div class='chc'><input type='text' name='city' placeholder='City' value='<?php echo $var->city; ?>' /></div>
<div class='ch1'>Pin Code</div>
<div class='chc'><input type='text' name='pin' placeholder='Pin Code' value='<?php echo $var->pin; ?>' /></div>
</div>
<div class='ch1'>Address</div>
<div class='chc'><input type='text' name='address' placeholder='Address' value='<?php echo $var->address; ?>' /></div>
<div class='ch1'>State</div>
<div class='chc'><input type='text' name='state' placeholder='State' value='<?php echo $var->state; ?>' /></div>
</form>
<div class='emo'>
<div class='yes' onclick='f3.submit()'>Save changes</div>&nbsp;<a href='account.php'><div class='no'>Cancel</div></a>
</div>
<?php } else { ?>
<div class='con3'>
<div class='ch1'>Name</div>
<div class='chc'><?php echo $var->name; ?></div>
<div class='ch1'>Phone</div>
<div class='chc'><?php echo $var->phone; ?></div>
<div class='ch1'>Email</div>
<div class='chc'><?php echo $var->email; ?></div>
</div><div class='con3'>
<div class='ch1'>Country</div>
<div class='chc'><?php echo $var->country; ?></div>
<div class='ch1'>City</div>
<div class='chc'><?php echo $var->city; ?></div>
<div class='ch1'>Pin Code</div>
<div class='chc'><?php echo $var->pin; ?></div>
</div>
<div class='ch1'>Address</div>
<div class='chc'><?php echo $var->address; ?></div>
<div class='ch1'>State</div>
<div class='chc'><?php echo $var->state; ?></div>
<?php } ?>
<div id='faq_'>
<a href='http://support.orntel.com'><div id='faq'>Frequently Asked Questions</div></a>
<a class='qst' href='http://support.orntel.com/question/What-is-Max-Login-in-Account-setting.php'>What is Max-Login in Account setting?</a>
<a class='qst' href='http://support.orntel.com/question/What-is-Max-Duration-in-Account-setting.php'>What is Max-Duration in Account setting?</a>
<a class='qst' href='http://support.orntel.com/support/question/How-to-delete-Orntel-Account.php'>How to delete Orntel Account?</a>
<a class='qst' href='http://support.orntel.com/support/question/Why-should-i-set-Alternate-Number-in-account-setting.php'>Why should i set `Alternate Number` in account setting?</a>
<a class='qst' href='http://support.orntel.com/question/Why-should-i-set-Security-Question.php'>Why should i set `Security Question`?</a>
<a class='qst' href='http://support.orntel.com/question/Should-i-set-Pre-Payment-Data.php'>Should i set `Pre Payment Data`?</a>
<a class='qst' href='http://support.orntel.com/question/Is-it-necessary-to-give-Personal-Data.php'>Is it necessary to give `Personal Data`?</a>
</div>
</section>
<?php include 'footer.php';?>