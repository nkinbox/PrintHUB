<?php include 'header.php';
function error() {
switch($GLOBALS['err']) {
case 1:
echo "<div class='e_r'>An error occured while Uploading file.</div>";
break;
case 2:
echo "<div class='e_r'>Max file size is 20MB. File size exceeded.</div>";
break;
case 3:
echo "<div class='e_r'>File extention is not .pdf (.PDF is also not accepted).</div>";
break;
case 4:
echo "<div class='e_r'>File type is not pdf.</div>";
break;
case 5:
echo "<div class='e_r'>File is a damaged pdf.</div>";
break;
case 6:
echo "<div class='e_r'>This upload seems to be invalid.</div>";
break;
case 7:
echo "<div class='e_r'>File link is invalid or file does not exists.</div>";
break;
case 8:
echo "<div class='e_r'>File was not replaced.</div>";
break;
case 9:
echo "<div class='e_r'>No file found to print.</div>";
break;
case 10:
echo "<div class='e_r'>Invalid color mode selected.</div>";
break;
case 11:
echo "<div class='e_r'>Invalid printing mode.</div>";
break;
case 12:
echo "<div class='e_r'>Invalid number of copies field.</div>";
break;
case 13:
echo "<div class='e_r'>Invalid printer selected.</div>";
break;
case 14:
echo "<div class='e_r'>Continuous print commands are not allowed. wait 60 seconds.</div>";
break;
default:
echo "";
}}
?>
<section>
<?php 
$i=0;
while($i<3) { if(!empty($_COOKIE['perr' .$i])) { $err=$_COOKIE['perr' .$i]; error(); } $i=$i+1; }
if(empty($_COOKIE['perr'])) { $err=0; } else { $err=$_COOKIE['perr']; } error(); ?>
<form name='f1' enctype='multipart/form-data' action='v.php' method='POST'>
<div class='g_c'><div class='g_co'><div class='g'>
<div class='g_h'>Upload PDF</div>
<input type='hidden' name='MAX_FILE_SIZE' value='50000000' />
<?php if(empty($_GET['file1'])) { ?>
<input class='file' name='filecontainer[]' type='file' accept='application/pdf' />
<div class='add' id='a2' onclick='add(2)'></div>
<input style='display: none' id='fc2' class='file' name='filecontainer[]' type='file' accept='application/pdf' />
<div style='display: none' class='add' id='a3' onclick='add(3)'></div>
<input style='display: none' id='fc3' class='file' name='filecontainer[]' type='file' accept='application/pdf' />
<?php } else { if(empty($_GET['file2'])) { ?>
<input type='text' class='upd' name='filen1' value='<?php getfilename(1); ?>' readonly />
<input type='hidden' name='file1' value='<?php echo strip_tags($_GET['file1']); ?>' />
<div class='add' id='a2' onclick='add(2)'></div>
<input style='display: none' id='fc2' class='file' name='filecontainer[]' type='file' accept='application/pdf' />
<div style='display: none' class='add' id='a3' onclick='add(3)'></div>
<input style='display: none' id='fc3' class='file' name='filecontainer[]' type='file' accept='application/pdf' />
<?php } else { if(empty($_GET['file3'])) {?>
<input type='text' class='upd' name='filen1' value='<?php getfilename(1); ?>' readonly />
<input type='hidden' name='file1' value='<?php echo strip_tags($_GET['file1']); ?>' />
<input type='text' class='upd' name='filen2' value='<?php getfilename(2); ?>' readonly />
<input type='hidden' name='file2' value='<?php echo strip_tags($_GET['file2']); ?>' />
<div class='add' id='a3' onclick='add(3)'></div>
<input style='display: none' id='fc3' class='file' name='filecontainer[]' type='file' accept='application/pdf' />
<?php } else {?>
<input type='text' class='upd' name='filen1' value='<?php getfilename(1); ?>' readonly />
<input type='hidden' name='file1' value='<?php echo strip_tags($_GET['file1']); ?>' />
<input type='text' class='upd' name='filen2' value='<?php getfilename(2); ?>' readonly />
<input type='hidden' name='file2' value='<?php echo strip_tags($_GET['file2']); ?>' />
<input type='text' class='upd' name='filen3' value='<?php getfilename(3); ?>' readonly />
<input type='hidden' name='file3' value='<?php echo strip_tags($_GET['file3']); ?>' />
<?php }}} ?>
<div class='ofl'>PDF file size should be less than 50MB.</div>
<div class='ofl'>File extention should be .pdf (.PDF is invalid)</div>
<div class='ofl'>If you PDF contains Camera clicked images. Make sure to auto-correct images otherwise printout will be very dark.</div>
<div class='ofl'>Optimize PDF before uploading and Save you data usage.</div>
</div></div></div><div class='g_c'><div class='g_co'><div class='g'>
<div class='g_h'>Select Printer</div>
<?php showprinters();
$i=1;
foreach($printers as $printer) {
echo "<div>
<input id='p" .$i. "' type='radio' name='printer' value='" .$printer['pid']. "'";
if($i == 1)
echo " checked";
echo " />
<label for='p" .$i. "'>" .$printer['name']. "</label>
<div class='p_i'>";
if($printer['status'] == 1)
echo "<span id='on'>Online</span>";
elseif($printer['status'] == 2)
echo "<span id='off'>Not Working</span>";
else
echo "<span id='off'>Offline</span>";
echo "<span>Model -&nbsp;</span>" .$printer['model']. "<br>
<a href='" .$printer['map']. "' target='_blank'><span>Location -&nbsp;</span>" .$printer['location']. "</a>";
if($printer['status'] == 0)
echo "<div class='ofl'>Printer is offline. Your task will be added in Queue.</div>";
echo "</div></div>";
$i=$i+1;
}
?>
</div></div></div><div class='g_c'><div class='g_co'><div class='g'>
<div class='g_h'>Overview</div>

<input type='number' name='noc' placeholder='Number of copies' style='background-position: 0 -96px' />
<select name='ptype' style='background-position: 0 -128px'>
<option value='bw'>Black & White</option>
</select>

<select name='mode' style='background-position: 0 -128px'>
<option value='free'>Free Print</option>
<option value='paid'>Paid Print</option>
</select>
</form>
<div id='fin' onclick='submitf();'>Proceed</div>
<br style='clear: both' /><br />
<div class='ofl'>You can check status of your print task in your Account tab. You will be notified by SMS when task is completed.</div>
</div></div></div>
<div id='faq_'>
<a href='http://support.orntel.com'><div id='faq'>Frequently Asked Questions</div></a>
<a class='qst' href='http://support.orntel.com/question/Difference-between-Free-print-and-Paid-Print.php'>Difference between Free print and Paid Print.</a>
<a class='qst' href='http://support.orntel.com/question/What-is-the-procedure-of-collecting-Printed-Documents.php'>What is the procedure of collecting Printed Documents?</a>
<a class='qst' href='http://support.orntel.com/question/What-is-Print-id.php'>What is Print id?</a>
<a class='qst' href='http://support.orntel.com/question/How-to-create-My-own-Predefined-List-on-Orntel-Printhub.php'>How to create `My own Predefined List` on Orntel Printhub?</a>
<a class='qst' href='http://support.orntel.com/question/Can-i-Print-DOC-file-using-Orntel-Printhub.php'>Can i Print .DOC file using Orntel Printhub?</a>
<a class='qst' href='http://support.orntel.com/question/Can-my-Friend-Collect-my-Printouts.php'>Can my Friend Collect my Printouts?</a>
</div>
</section>
<?php include 'footer.php';?>