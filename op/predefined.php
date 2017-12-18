<?php include 'header.php';
if(empty($_COOKIE['uerr']))
$err=0;
else
$err=$_COOKIE['uerr'];
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
echo "<div class='e_r'>File already exists.</div>";
break;
case 8:
echo "<div class='e_r'>File was not replaced.</div>";
break;
case 9:
echo "<div class='e_r'>Number of pages is incorrect.</div>";
break;
case 10:
echo "<div class='e_r'>File name is invalid.<br />Special characters not allowed. Max 20 Char</div>";
break;
case 11:
echo "<div class='e_r'>File description is invalid.<br />Special characters not allowed. Max 50 Char</div>";
break;
case 12:
echo "<div class='e_r'>Atleast one tag should be added.</div>";
break;
default:
echo "";
}
}
?>
<section>
<div id='cp_c'>
<div class='c_h'><?php
if(!empty($_COOKIE['fileid']))
echo "Edit Your predefined list";
else
echo "Create Your predefined list";
?></div>
<?php error(); ?>
<form name='f1' enctype='multipart/form-data' action='v.php' method='POST'>
<div class='c_in'>
<?php if(empty($_COOKIE['fileid'])) { ?>
<div>
<?php } else { ?>
<div id='id1'>File exists on the server
<div class='d_arr'></div><a href='#' onclick='addfile(); return false;'><div class='_ll'>Click to update file</div></a></div>
<div id='id2' style='display: none'>
<input type='hidden' name='replace' value='1' />
<?php } ?>
<input type='hidden' name='MAX_FILE_SIZE' value='50000000' />
<input class='input_ file' name='filecontainer[]' type='file' accept='application/pdf' />
<div class='d_arr'></div><div class='_ll'>Upload Pdf file (Max 50MB)</div>
</div></div>
<div class='c_in'>
<?php predval(); ?>
<input id='pag' class='input_' type='text' name='pages' placeholder='No of Pages'<?php
if(!empty($_COOKIE['_fp']))
echo " value='" .$_COOKIE['_fp']. "'";
elseif(!empty($value))
echo " value='" .$value[0]['pages']. "'";
?> />
<div class='d_arr'></div><div class='_ll'>Number of pages in file</div>
</div>
<div class='c_in'>
<input id='nam' class='input_' type='text' name='filename' placeholder='File name'<?php
if(!empty($_COOKIE['_fn']))
echo " value='" .$_COOKIE['_fn']. "'";
elseif(!empty($value))
echo " value='" .$value[0]['name']. "'";
?> />
<div class='d_arr'></div><div class='_ll'>File Name (Max 20 Char)</div>
</div>
<div class='c_in'>
<input id='des' class='input_' type='text' name='filedes' placeholder='File Description'<?php
if(!empty($_COOKIE['_fd']))
echo " value='" .$_COOKIE['_fd']. "'";
elseif(!empty($value))
echo " value='" .$value[0]['description']. "'";
?> />
<div class='d_arr'></div><div class='_ll'>About file content<br />(Max 50 Char)</div>
</div>
<div class='c_in c_at'>
<?php
$arr=findtags();
$i=1;
?>
<label for='c1' class='cl' onclick='tagshow("c1")'><input id='c1' type='radio' name='category' value='study'<?php pcatg(1,$arr[1]); ?> />Study Material</label
><label for='c2' class='cl' onclick='tagshow("c2")'><input id='c2' type='radio' name='category' value='other'<?php pcatg(2,$arr[1]); ?> />Other</label>
<div class='d_arr'></div><div class='_ll'>Choose Category</div>
</div>
<div class='c_in'>
<div id='tag_c'<?php pcatg(3,$arr[1]); ?>>
<div class='_ll'>Add Branch tag</div><div class='u_arr'></div>
<div class='tag_co'>
<?php showtags("bra", $arr[0], $arr[1]); ?>
</div>
<div class='_ll'>Add Semester tag</div><div class='u_arr'></div>
<div class='tag_co'>
<?php showtags("sem", $arr[0], $arr[1]); ?>
</div>
</div>
</div>
</form>
<div id='c_bt' onclick='f1.submit()'>Finish</div>
</div>
</section>
<?php include 'footer.php';?>