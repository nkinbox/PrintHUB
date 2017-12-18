<?php include '../library/functions.php';
function  addpromotioncredits($userid, $byuserid, $fileid) {
$con=new dbconnect("db_counter");
$con->q_exe("select sno from promotion_counter where fileid = :fileid and userid = :userid and byuserid = :byuserid limit 1;",array("fileid"=>$fileid,"userid"=>$userid,"byuserid"=>$byuserid));
if(count($con->rows) == 0) {
$con->q_exe("INSERT INTO `db_counter`.`promotion_counter` (`sno`, `fileid`, `userid`, `byuserid`) VALUES ('0', :fileid, :userid, :byuserid);",array("fileid"=>$fileid,"userid"=>$userid,"byuserid"=>$byuserid));
unset($con);
$var = new cashier($userid);
$var->credit(5,"paid");
unset($var);
} else {unset($con);}
}
function howmuch($pages) {
switch($pages) {
case $pages<21:
$amount = $pages;
break;
case $pages<41:
$amount = round($pages*0.9);
break;
case $pages<61:
$amount = round($pages*0.85);
break;
case $pages<81:
$amount = round($pages*0.8);
break;
case $pages>80:
$amount = round($pages*0.7);
break;
default:
$amount = round($pages*0.7);
}
return $amount;
}
function unlinkfiles($pid) {
$con=new dbconnect("db_counter");
$con->q_exe("select fileid from print_task where pid = :pid limit 3;",array("pid"=>$pid));
$rows=$con->rows;
unset($con);
$con=new dbconnect("db_printhub");
foreach($rows as $row) {
$con->q_exe("select predefined from pdf_files where fileid = :fn limit 1;",array("fn"=>$row['fileid']));
if($con->rows[0]['predefined'] == "0")
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `linked` = 0 WHERE `pdf_files`.`predefined` = 0 and `pdf_files`.`fileid` = :fileid limit 1;",array("fileid"=>$row['fileid']));
}
unset($con);
}
function getphnum($userid) {
$con=new dbconnect("db_credentials");
$con->q_exe("select phone from account where userid = :u limit 1;",array("u"=>$userid));
$rows=$con->rows;
unset($con);
if(count($rows) == 0)
return "+918468054060";
else
return $rows[0]['phone'];
}
if(empty($_GET['process'])) {
echo "0";
}
if($_GET['process'] == "login") {
if(!empty($_GET['u']) && !empty($_GET['p']) && preg_match('/^[0-9]+$/i', $_GET['u']) && preg_match('/^[0-9]+$/i', $_GET['p'])) {
$con=new dbconnect("db_printhub");
$con->q_exe("select pid from printers where username = :u and password = :p limit 1;",array("u"=>$_GET['u'],"p"=>$_GET['p']));
$rows=$con->rows;
if(count($rows) == 0) {
unset($con);
echo "2";
} else {
echo $rows[0]['pid'];
$con->q_exe("UPDATE `db_printhub`.`printers` SET `status` = '1', time = :time WHERE `printers`.`pid` = :pid limit 1;",array("pid"=>$rows[0]['pid'],"time"=>time()));
unset($con);
}} else echo "1";
} elseif($_GET['process'] == "gettask") {
$pid = $_GET['pid'];
$con=new dbconnect("db_printhub");
$con->q_exe("UPDATE `db_printhub`.`printers` SET `status` = '1', time = :time WHERE `printers`.`pid` = :pid limit 1;",array("pid"=>$pid,"time"=>time()));
unset($con);
$reprint = "n";
$con=new dbconnect("db_counter");
$con->q_exe("select pid, copies from print_task where prnid = :pid and stage = 0 and reprint = 1 limit 1;",array("pid"=>$pid));
if(count($con->rows) == 1) {
$printid=$con->rows[0]['pid'];
$copies=$con->rows[0]['copies'];
$reprint = "y";
} else {
$con->q_exe("select pid, copies from print_task where prnid = :pid and stage = 0 and reprint = 0 limit 1;",array("pid"=>$pid));
if(count($con->rows) == 1) {
$printid=$con->rows[0]['pid'];
$copies=$con->rows[0]['copies'];
} else {
unset($con);
echo "notask";
exit;
}}
$con->q_exe("select fileid, userid from print_task where pid = :pid limit 3;",array("pid"=>$printid));
$files=$con->rows;
$con->q_exe("UPDATE `db_counter`.`print_task` SET `stage` = '1' WHERE `print_task`.`pid` = :pid limit 3;",array("pid"=>$printid));
unset($con);
$con=new dbconnect("db_printhub");
$con->q_exe("UPDATE `db_printhub`.`printers` SET `status` = '1', time = :time WHERE `printers`.`pid` = :pid limit 1;",array("pid"=>$pid,"time"=>time()));
$data=array();
$data['file']=array();
$data['predefined']=array();
foreach($files as $fileid) {
$con->q_exe("select filename, userid, predefined from pdf_files where fileid = :fileid limit 1;",array("fileid"=>$fileid['fileid']));
$data['file'][]=$con->rows[0]['filename'];
if($con->rows[0]['predefined'] == "1") {
$data['predefined'][]="y";
addpromotioncredits($con->rows[0]['userid'],$fileid['userid'],$fileid['fileid']);
}
else
$data['predefined'][]="n";
}
unset($con);
$task="";
foreach($data['file'] as $key=>$value) {
if($task=="")
$task="f:" .$value. ":p:" .$data['predefined'][$key]. ":c:" .$copies;
else
$task.=",f:" .$value. ":p:" .$data['predefined'][$key]. ":c:" .$copies;
}
$task=$reprint. "." .$printid. ";" . $task;
echo $task;
} elseif($_GET['process'] == "print") {
$data=array();
$data['filename']=array();
$data['predefined']=array();
$data['pages']=array();
$data['fileid']=array();
$task = explode(";",$_GET['task']);
foreach($task as $key=>$part) {
if($key == 0) {
$var=explode(".",$part);
$printid=$var[1];
$reprint=$var[0];
} else {
$var=explode(".pdf",$part);
$data['filename'][]=$var[0];
$data['pages'][]=$var[1];
}}
$con=new dbconnect("db_printhub");
foreach($data['filename'] as $key=>$value) {
$con->q_exe("select fileid, predefined from pdf_files where filename = :fn limit 1;",array("fn"=>$value));
$data['fileid'][$key]=$con->rows[0]['fileid'];
if($con->rows[0]['predefined'] == "1")
$data['predefined'][$key]="y";
else
$data['predefined'][$key]="n";
$con->q_exe("UPDATE `db_printhub`.`predefined` SET `pages` = :pages WHERE `predefined`.`fileid` = :fileid limit 1;",array("pages"=>$data['pages'][$key],"fileid"=>$data['fileid'][$key]));
}
unset($con);
$amount = 0;
$con=new dbconnect("db_counter");
foreach($data['fileid'] as $key=>$value) {
$con->q_exe("UPDATE `db_counter`.`print_task` SET `pages` = :pages, `stage` = 2 WHERE `print_task`.`pid` = :pid and `print_task`.`fileid` = :fileid limit 1;",array("pages"=>$data['pages'][$key],"pid"=>$printid,"fileid"=>$value));
$amount=$amount+intval($data['pages'][$key]);
}
if($reprint == "y") {
echo "y";
unset($con);
exit;
}
$con->q_exe("select userid, copies, free from print_task where pid = :pid limit 1;",array("pid"=>$printid));
$userid=$con->rows[0]['userid'];
$amount=$amount*intval($con->rows[0]['copies']);
$amount=howmuch($amount);
if($con->rows[0]['free'] == "1")
$mode="free";
else
$mode="paid";
$con->q_exe("select userid, amount from debit_counter where pid = :pid limit 1;",array("pid"=>$printid));
if(count($con->rows) == 1 && $con->rows[0]['userid'] == $userid && $con->rows[0]['amount'] == $amount) {
echo "y";
unset($con);
exit;
}
$var = new cashier($userid);
if($var->debit($amount,$mode)) {
unset($var);
$con->q_exe("INSERT INTO `db_counter`.`debit_counter` (`sno`, `userid`, `pid`, `amount`, `time`) VALUES ('0', :userid, :pid, :amount, :time);",array("userid"=>$userid,"pid"=>$printid,"amount"=>$amount,"time"=>time()));
unset($con);
echo "y";
} else {
unset($var);
$con->q_exe("UPDATE `db_counter`.`print_task` SET `stage` = 4 WHERE `print_task`.`pid` = :pid limit 3;",array("pid"=>$printid));
unset($con);
unlinkfiles($printid);
$var=new mailbox($userid,1);
$var->subject="Print task Rejected";
$var->content="Your printing task (" .$printid. ") has been rejected Because of Low credits.";
$var->send();
echo "n";
$phone=getphnum($userid);
send_msg($phone,3);
}
} elseif($_GET['process'] == "printed") {
$data=array();
$data['filename']=array();
$data['fileid']=array();
$task = explode(";",$_GET['task']);
foreach($task as $key=>$part) {
if($key == 0) {
$var=explode(".",$part);
$printid=$var[1];
$reprint=$var[0];
} else {
$var=explode(".pdf",$part);
$data['filename'][]=$var[0];
}}
$con=new dbconnect("db_printhub");
foreach($data['filename'] as $key=>$value) {
$con->q_exe("select fileid, predefined from pdf_files where filename = :fn limit 1;",array("fn"=>$value));
$data['fileid'][$key]=$con->rows[0]['fileid'];
if($con->rows[0]['predefined'] == "0")
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `linked` = 0 WHERE `pdf_files`.`predefined` = 0 and `pdf_files`.`fileid` = :fileid limit 1;",array("fileid"=>$data['fileid'][$key]));
}
unset($con);
$con=new dbconnect("db_counter");
if($reprint == "y")
$con->q_exe("UPDATE `db_counter`.`print_task` SET `stage` = 5 WHERE `print_task`.`pid` = :pid limit 3;",array("pid"=>$printid));
else
$con->q_exe("UPDATE `db_counter`.`print_task` SET `stage` = 3 WHERE `print_task`.`pid` = :pid limit 3;",array("pid"=>$printid));
$con->q_exe("select userid from print_task where pid = :pid limit 1;",array("pid"=>$printid));
$userid=$con->rows[0]['userid'];
unset($con);
echo "ok";
$phone=getphnum($userid);
send_msg($phone,2,$printid);
} elseif($_GET['process'] == "reject") {
unlinkfiles($_GET['printid']);
$con=new dbconnect("db_counter");
$con->q_exe("UPDATE `db_counter`.`print_task` SET `stage` = 4 WHERE `print_task`.`pid` = :pid limit 3;",array("pid"=>$_GET['printid']));
$con->q_exe("select userid from print_task where pid = :pid limit 1;",array("pid"=>$_GET['printid']));
$userid=$con->rows[0]['userid'];
unset($var);
$var=new mailbox($userid,1);
$var->subject="Print task Rejected";
$var->content="Your printing task (" .$_GET['printid']. ") has been rejected Because of Unknown reasons.";
$var->send();
$phone=getphnum($userid);
send_msg($phone,3);
} else echo "0";
exit;
?>