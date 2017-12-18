<?php
include 'library/functions.php';
if(empty($_COOKIE['priv'])) {
$priv="none";
if(!empty($_GET['u']) && !empty($_GET['p']) && preg_match('/^[a-z0-9]+$/i', $_GET['u']) && preg_match('/^[a-z0-9]+$/i', $_GET['p'])) {
$sid=md5(sha1(sha1(time() . mt_rand(10,100))) . time());
$con=new dbconnect("db_manage");
$con->q_exe("select priv from handlers where username = :u and password = :p limit 1;",array("u"=>$_GET['u'],"p"=>$_GET['p']));
if(count($con->rows) == 1) {
$priv=$con->rows[0]['priv'];
$con->q_exe("UPDATE  `db_manage`.`handlers` SET  `sessionid` =  :sid WHERE  `handlers`.`priv` = :priv limit 1;",array("sid"=>$sid,"priv"=>$priv));
}
unset($con);
}
if($priv == "none") {
echo "To access this Directory Authentication is Required";
exit;
}
setcookie("priv",$sid,0,'/','www.nojok.in',false,true);
header('Location: index.php');
exit;
} else {
$con=new dbconnect("db_manage");
$con->q_exe("select priv from handlers where sessionid = :sid limit 1;",array("sid"=>$_COOKIE['priv']));
$priv=$con->rows[0]['priv'];
unset($con);
switch($priv) {
case "pred":
if(empty($_GET['step']))
$step=0;
else
$step=$_GET['step'];
switch($step) {
case 0:
echo "<a href='index.php?step=1'>Check Unverified Files</a><br />
<a href='index.php?step=2'>Print Unverified Files</a><br />
<a href='index.php?step=3'>Verify Files</a>";
break;
case 1:
$con=new dbconnect("db_printhub");
$con->q_exe("select fileid, filename, userid from pdf_files where predefined = 1 and verified = 0 limit 1;",array());
$rows=$con->rows;
if(count($rows) == 0) {
echo "No unaltered File left.<br />";
echo "<a href='index.php?step=1'>Refresh</a><br />";
} else {
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `verified` = 2 WHERE `pdf_files`.`fileid` = :fileid limit 1;", array("fileid"=>$rows[0]['fileid']));
echo "<a href='http://nojok.in/files/" .$rows[0]['filename']. ".pdf' target='_blank'>Download PDF</a><br />
FileId : " .$rows[0]['fileid']. "<br />
Userid : " .$rows[0]['userid']. "<br />
<a href='http://www.orntel.com/pepsi.php?password=' target='_blank'>Modify file data</a><br />";
echo "<a href='index.php?step=1'>Next</a><br />";
}
unset($con);
break;
case 2:
$con=new dbconnect("db_printhub");
$con->q_exe("select fileid from pdf_files where verified = 2 limit 1;",array());
if(count($con->rows) == 0) {
unset($con);
echo "No un-Printed File left.<br />";
echo "<a href='index.php?step=2'>Refresh</a><br />";
} else {
$fileid=$con->rows[0]['fileid'];
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `verified` = 3 WHERE `pdf_files`.`fileid` = :fileid limit 1;", array("fileid"=>$fileid));
unset($con);
echo "<a href='http://printhub.orntel.com/print.php?file1=" .$fileid. "' target='_blank'>Print " .$fileid. " PDF</a><br />";
echo "<a href='index.php?step=2'>Next</a><br />";
}
break;
case 3:
if(empty($_GET['pid'])) {
echo "<form action='' method='get'>
<input type='hidden' name='step' value='3'>
<input type='text' name='pid'>
<select name='pro'>
<option value='1'>Verify</option>
<option value='0'>Restart</option>
<input type='submit' value='go'>
</select>
</form>";
} else {
$con=new dbconnect("db_counter");
$con->q_exe("select fileid from print_task where pid = :pid limit 1;",array("pid"=>$_GET['pid']));
$rows=$con->rows;
unset($con);
if(count($rows) == 1) {
$con=new dbconnect("db_printhub");
if($_GET['pro'] == "1") {
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `verified` = 1 WHERE `pdf_files`.`fileid` = :fileid limit 1;", array("fileid"=>$rows[0]['fileid']));
echo "verified";
} elseif ($_GET['pro'] == "0") {
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `verified` = 0 WHERE `pdf_files`.`fileid` = :fileid limit 1;", array("fileid"=>$rows[0]['fileid']));
echo "Restarted";
} else echo "Invalid Process.";
unset($con);
} else echo "No file file Found.";
echo "<br /><a href='index.php?step=3'>Next</a><br />";
}
break;
default:
echo "Error";
}
break;
case "paym":
echo "<form action='' method='get'>
Grab Payment History of Userid : <input type='text' name='userid'>
<input type='submit' value='go'>
</form><form action='' method='get'>
Grab Debit History of Userid : <input type='text' name='duid'>
<input type='submit' value='go'>
</form><form action='' method='get'>
Payment Gateway Orderid : <input type='text' name='orderid'>
<input type='submit' value='go'>
</form>";
if(!empty($_GET['duid'])) {
$con=new dbconnect("db_counter");
$con->q_exe("select * from debit_counter where userid = :userid limit 5;",array("userid"=>$_GET['duid']));
$rows = $con->rows;
unset($con);
echo "Printid => Amount , Time<br>";
foreach($rows as $row) {
echo $row['pid']. " => <b>" .$row['amount']. "</b> , " .date('D, d M Y H:i:s',$row['time']). "<br>";
}
}
if(!empty($_GET['oid'])) {
if($_GET['lab'] == "ok") {
$con=new dbconnect("db_counter");
$con->q_exe("UPDATE  `db_counter`.`cash_counter` SET  `res_time` =  :time, `lab` = 1 WHERE  `cash_counter`.`orderid` = :orderid limit 1;", array("time"=>time(),"orderid"=>$_GET['oid']));
unset($con);
echo "Done..";
}
if($_GET['lab'] == "force") {
$con=new dbconnect("db_counter");
$con->q_exe("select userid, status from cash_counter where orderid = :orderid limit 1;",array("orderid"=>$_GET['oid']));
$userid=$con->rows[0]['userid'];
$status=$con->rows[0]['status'];
if($status == "0") {
$con->q_exe("UPDATE  `db_counter`.`cash_counter` SET  `status` = :status, `amount` = :amount, `response` = :response, `res_time` =  :time, `lab` = 1 WHERE  `cash_counter`.`orderid` = :orderid limit 1;", array("status"=>"1","amount"=>$_GET['amount'],"response"=>"Forced response","time"=>time(),"orderid"=>$_GET['oid']));
unset($con);
$v=new cashier($userid);
$v->credit(intval($_GET['amount']),"paid");
$var=new mailbox($userid,1);
$var->subject="Payment Successful";
$var->content="Payment of " .$_GET['amount']. " INR was made successfully to Orntel. Order Id is " .$_GET['oid']. ". Your Account has been Credited.";
$var->send();
echo "Done..";
} else {
unset($con);
echo "Not Done..";
}
}
}
if(!empty($_GET['userid'])) {
$con=new dbconnect("db_counter");
$con->q_exe("select * from cash_counter where userid = :userid limit 5;",array("userid"=>$_GET['userid']));
$rows = $con->rows;
unset($con);
foreach($rows as $row) {
echo "<br/>#Orderid : <b>" .$row['orderid']. "</b> , Status = " .$row['status']. " , Amount = " .$row['amount']. "<br/>
Request Time : " .date('D, d M Y H:i:s',$row['req_time']). " , Response Time : " .date('D, d M Y H:i:s',$row['res_time']) . "<br/>";
if($row['lab'] == "0")
echo "Unverified";
else
echo "verified";
echo "<br/>" .$row['response']. "<hr/>";
}
}
if(!empty($_GET['orderid'])) {
$con=new dbconnect("db_counter");
$con->q_exe("select * from cash_counter where orderid = :orderid limit 1;",array("orderid"=>$_GET['orderid']));
$rows = $con->rows;
unset($con);
foreach($rows as $row) {
echo "<br/>#Orderid : <b>" .$row['orderid']. "</b> , Userid : " .$row['userid']. ", Status = " .$row['status']. " , Amount = " .$row['amount']. "<br/>
Request Time : " .date('D, d M Y H:i:s',$row['req_time']). " , Response Time : " .date('D, d M Y H:i:s',$row['res_time']) . "<br/>";
if($row['lab'] == "0")
echo "Unverified";
else
echo "verified";
echo "<br/>" .$row['response']. "<hr/>";
echo "<a href='index.php?oid=" .$row['orderid']. "&lab=ok'>Its Okey</a>
<form action='' method='get'>
<input type='hidden' name='oid' value='" .$row['orderid']. "'>
<input type='hidden' name='lab' value='force'>
Amount : <input type='text' name='amount'>
<input type='submit' value='Force match'>
</form>";
}
}
break;
case "repr":
if(empty($_GET['step']))
$step=0;
else
$step=$_GET['step'];
switch($step) {
case 0:
echo "<a href='index.php?step=1'>Read Reprint request.</a><br/>
<a href='index.php?step=2'>Respond to request.</a><br/>
<a href='index.php?step=3'>User Reprint history.</a>";
break;
case 1:
$con=new dbconnect("db_counter");
$con->q_exe("select sno, userid from reprint_task where reprinted = 0 limit 1;",array());
if(count($con->rows)==0) {
unset($con);
echo "No reprint request.";
} else {
$rows=$con->rows[0];
$userid=$rows['userid'];
$con->q_exe("UPDATE `db_counter`.`reprint_task` SET `reprinted` = '2' WHERE `reprint_task`.`sno` = :sno limit 1;",array("sno"=>$rows['sno']));
$con->q_exe("select pid, reason, reprinted, time from reprint_task where userid = :userid order by time desc limit 5;",array("userid"=>$rows['userid']));
$rows=$con->rows;
unset($con);
$con=new dbconnect("db_credentials");
$con->q_exe("select phone, altphone from account where userid = :uid limit 1;",array("uid"=>$userid));
echo "Userid : " .$userid. ", Phone : " .$con->rows[0]['phone']. ", Altphone : " .$con->rows[0]['altphone'];
unset($con);
foreach($rows as $row) {
echo "<hr/>PrintId : <b>" .$row['pid']. "</b> , <i>" .date('D, d M Y H:i:s',$row['time']). "</i> , <u>";
switch($row['reprinted']) {
case 0:
echo "Not Responded";
break;
case 1:
echo "Reprinted";
break;
case 2:
echo "Processing";
break;
case 3:
echo "Rejected";
}
echo "</u><br/>" .$row['reason']. "<br/><br/>";
}
}
echo "<br/><a href='index.php?step=1'>Next</a>";
break;
case 2:
$con=new dbconnect("db_counter");
$con->q_exe("select sno, pid, userid from reprint_task where reprinted = 2 limit 1;",array());
if(count($con->rows)==0) {
unset($con);
echo "No reprint request.";
} else {
$rows=$con->rows[0];
$userid=$rows['userid'];
$pid=$rows['pid'];
if(!empty($_GET['pro'])) {
switch($_GET['pro']) {
case 1:
$var=new reprint_task($userid,$pid);
if($var->errorid == 0) {
$var->allow_reprint();
$con->q_exe("UPDATE `db_counter`.`reprint_task` SET `reprinted` = '1' WHERE `reprint_task`.`sno` = :sno limit 1;",array("sno"=>$rows['sno']));
unset($con);
$var=new mailbox($userid,1);
$var->subject="Reprint Request";
$var->content="Your Reprint request for print id " .$pid. " has been Accepted.";
$var->send();
echo "reprinted";
} else {
echo "error";
unset($con);
}
break;
case 2:
$con->q_exe("UPDATE `db_counter`.`reprint_task` SET `reprinted` = '3' WHERE `reprint_task`.`sno` = :sno limit 1;",array("sno"=>$rows['sno']));
unset($con);
$var=new mailbox($userid,1);
$var->subject="Reprint Request";
$var->content="Your Reprint request for print id " .$pid. " has been Rejected.";
$var->send();
echo "rejected";
break;
default:
echo "error";
unset($con);
}
echo "<br/><a href='index.php?step=2'>Next</a>";
} else {
$con->q_exe("select pid, reason, reprinted, time from reprint_task where userid = :userid order by time desc limit 5;",array("userid"=>$userid));
$rows=$con->rows;
unset($con);
echo "<h3>Current request : " .$pid. "</h3>
<form action='' method='get'>
<input type='hidden' name='step' value='2'>
<select name='pro'>
<option value='1'>Reprint</option>
<option value='2'>Reject</option>
</select>
<input type='submit' value='go'>
</form><hr/>
";
$con=new dbconnect("db_credentials");
$con->q_exe("select phone, altphone from account where userid = :uid limit 1;",array("uid"=>$userid));
echo "Userid : " .$userid. ", Phone : " .$con->rows[0]['phone']. ", Altphone : " .$con->rows[0]['altphone'];
unset($con);
foreach($rows as $row) {
echo "<hr/>PrintId : <b>" .$row['pid']. "</b> , <i>" .date('D, d M Y H:i:s',$row['time']). "</i> , <u>";
switch($row['reprinted']) {
case 0:
echo "Not Responded";
break;
case 1:
echo "Reprinted";
break;
case 2:
echo "Processing";
break;
case 3:
echo "Rejected";
}
echo "</u><br/>" .$row['reason']. "<br/><br/>";
}
}
}
echo "<br/><a href='index.php?step=2'>Next</a>";
break;
case 3:
if(empty($_GET['uid'])) {
echo "<form action='' method='get'>
<input type='hidden' name='step' value='3'>
Userid : <input type='text' name='uid'>
<input type='submit' value='go'>
</form>";
} else {
$userid=$_GET['uid'];
$con=new dbconnect("db_counter");
$con->q_exe("select pid, reason, reprinted, time from reprint_task where userid = :userid order by time desc limit 5;",array("userid"=>$userid));
$rows=$con->rows;
unset($con);
$con=new dbconnect("db_credentials");
$con->q_exe("select phone, altphone from account where userid = :uid limit 1;",array("uid"=>$userid));
echo "Userid : " .$userid. ", Phone : " .$con->rows[0]['phone']. ", Altphone : " .$con->rows[0]['altphone'];
unset($con);
foreach($rows as $row) {
echo "<hr/>PrintId : <b>" .$row['pid']. "</b> , <i>" .date('D, d M Y H:i:s',$row['time']). "</i> , <u>";
switch($row['reprinted']) {
case 0:
echo "Not Responded";
break;
case 1:
echo "Reprinted";
break;
case 2:
echo "Processing";
break;
case 3:
echo "Rejected";
}
echo "</u><br/>" .$row['reason']. "<br/><br/>";
}
}
break;
default:
echo "error";
}
break;
case "ovie":
$data=array();
$userid="0";
$ph="0";
$alt="";
$con=new dbconnect("db_printhub");
$con->q_exe("select count(userid) as var from pdf_files where predefined = 1 and linked = 1 and verified = 0;",array());
$data[0]=$con->rows[0]['var'];
$con->q_exe("select count(userid) as var from pdf_files where predefined = 1 and linked = 1;",array());
$data[5]=$con->rows[0]['var'];
$con->q_exe("select count(userid) as var from pdf_files where predefined = 0 and linked = 0;",array());
$data[6]=$con->rows[0]['var'];
unset($con);
$con=new dbconnect("db_counter");
$con->q_exe("select count(mid) as var from msgcounter;",array());
$data[12]=$con->rows[0]['var'];
$con->q_exe("select count(sno) as var from print_task where stage = 0;",array());
$data[1]=$con->rows[0]['var'];
$con->q_exe("select count(sno) as var, sum(copies*pages) as va from print_task where stage = 3;",array());
$data[13]=$con->rows[0]['var'];
$data[14]=$con->rows[0]['va'];
$con->q_exe("select count(sno) as var, sum(2*copies*pages) as va from print_task where stage = 5;",array());
$data[13]=$data[13]+$con->rows[0]['var'];
$data[14]=$data[14]+$con->rows[0]['va'];
$con->q_exe("select count(sno) as var from reprint_task where reprinted = 0;",array());
$data[2]=$con->rows[0]['var'];
$con->q_exe("select count(userid) as var from cash_counter;",array());
$data[7]=$con->rows[0]['var'];
$con->q_exe("select sum(amount) as var from debit_counter;",array());
$data[15]=$con->rows[0]['var'];
$con->q_exe("select count(userid) as var, sum(amount) as va from cash_counter where status = 1;",array());
$data[8]=$con->rows[0]['var'];
$data[9]=$con->rows[0]['va'];
unset($con);
$t=false;
$con=new dbconnect("db_credentials");
if(!empty($_POST['uid'])) {
$con->q_exe("select phone, altphone from account where userid = :uid limit 1;",array("uid"=>$_POST['uid']));
if(count($con->rows) == 1)
$userid=$_POST['uid'];
$ph=$con->rows[0]['phone'];
$alt=$con->rows[0]['altphone'];
$t=true;
} else {
if(!empty($_POST['ph'])) {
$ph="+91" . $_POST['ph'];
$con->q_exe("select userid from account where phone = :ph or altphone = :ph limit 1;",array("ph"=>$ph));
if(count($con->rows) == 1)
$userid=$con->rows[0]['userid'];
$t=true;
}
}
$con->q_exe("select count(userid) as var from account;",array());
$data[3]=$con->rows[0]['var'];
$con->q_exe("select count(userid) as var from session;",array());
$data[4]=$con->rows[0]['var'];
$con->q_exe("select sum(coins) as var from credits;",array());
$data[10]=$con->rows[0]['var'];
$con->q_exe("select sum(coins) as var from free_credits;",array());
$data[11]=$con->rows[0]['var'];
unset($con);
if($t) {
$con=new dbconnect("db_user");
$con->q_exe("select username, name from personal where userid = :userid limit 1;",array("userid"=>$userid));
$data[16]=$con->rows[0]['username'];
$data[17]=$con->rows[0]['name'];
unset($con);
}
echo "Total Unverified PDF files : " .$data[0]. "<br/>
Total Pending Tasks : " .$data[1]. "<br/>
Total Reprint Request : " .$data[2]. "<br/>
Total print id : " .$data[13]. "<br/>
Total printed pages : " .$data[14]. "<br/>
<br/>
Total Users : " .$data[3]. "<br/>
Total Logins : " .$data[4]. "<br/>
<br />
Total Predefined Files : " .$data[5]. "<br/>
Total Unlinked Files: " .$data[6]. "<br/>
<br/>
Total Payment Request : " .$data[7]. "<br/>
Total Successful Request : " .$data[8]. "<br/>
Total INR collected : " .$data[9]. "<br/>
Total coins debited : " .$data[15]. "<br/>
<br/>
Total paid credits remaining : " .$data[10]. "<br/>
Total free credits remaining : " .$data[11]. "<br/>
Total messages sent : " .$data[12]. "<br/>" .file_get_contents('http://api.mvaayoo.com/mvaayooapi/APIUtil?user=blockray7@gmail.com:flightlesswi&type=0'). "<br /><br /><hr/><br />
<form action='index.php' method='post'>
Altph: " .$alt. " , Ph: <input type='text' name='ph' value='" .$ph. "'> => userid: <input type='text' name='uid' value='" .$userid. "'>
<input type='submit' value='go'>
</form>
<form action='index.php' method='post'>
To : <input type='text' name='userid' value='" .$userid. "'><br/>
Subject (Max 20) : <input type='text' name='subject'><br/>
Content (Max 160) : <input type='text' name='content'><br/>
<input type='submit' value='go'>
</form>";
if(!empty($_POST['subject'])) {
$var=new mailbox($_POST['userid'],1);
$var->subject=$_POST['subject'];
$var->content=$_POST['content'];
$var->send();
echo "Mail sent..";
}
if($t) {
echo "<br/>" .$data[16]. " , " .$data[17]. "<br/>";
}
showprinters();
foreach($printers as $printer) {
if($printer['status'] == 1)
echo "<br/>" .$printer['name'] . "=>Online<br/>";
else
echo "<br/>" .$printer['name'] . "=>Offline<br/>";
}
break;
default:
echo "Security Error.";
}
}
?>