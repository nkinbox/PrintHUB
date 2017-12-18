<?php
if(empty($_SERVER['HTTP_REFERER']))
exit('[Invalid access]');
include '../library/functions.php';
if(empty($_POST)) {
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit; }
$bname = basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH), ".php");

$var=new session;
if($var->check_sessionid()) {
$userid=$var->userid;
if($bname == "predefined") {
$fileid="0";
if(!empty($_FILES['filecontainer']['name'][0])) {
if(!empty($_POST['replace']) && $_POST['replace'] == 1 && !empty($_COOKIE['fileid'])) {
$var=new upload("replace",$userid);
$var->fileid=$_COOKIE['fileid'];
} else $var=new upload("predefined",$userid);
$var->uploadfile(0,$_FILES['filecontainer']['error'][0]);
if($var->errorid != 0) {
setcookie("uerr",$var->errorid,time()+3,'/','.orntel.com',false,true);
header('Location: predefined.php');
exit;
} else {
setcookie("fileid",$var->fileid,0,'/','.orntel.com',false,true);
$fileid=$var->fileid;
}}
if($fileid!="0" || !empty($_COOKIE['fileid'])) {
$var=new predefined;
if(empty($_COOKIE['fileid']))
$var->fileid=$fileid;
else
$var->fileid=$_COOKIE['fileid'];
$var->userid=$userid;
if($var->add_predefined()) {
setcookie("fileid",'0',time()-43200,'/','.orntel.com',false,true);
header('Location: account.php');
exit;
}}
header('Location: predefined.php');
exit;
} elseif($bname == "print") {
$var=new printtask;
$var->userid=$userid;
$i=1;
$file=array();
$file['name']=array();
$file['id']=array();
while($i<4) {
if(!empty($_POST['file' . $i])) {
$var->fileid=$_POST['file' . $i];
if($var->check_fileid()) {
$file['id'][]=$var->fileid;
if(!empty($_POST['filen' . $i]))
$file['name'][]=$_POST['filen' . $i];
else
$file['name'][]="Unknown file";
}} $i=$i+1; }
$var2=new upload("undefined",$userid);
$i=0+count($file['id']);
$err=array();
foreach($_FILES['filecontainer']['error'] as $key => $error) {
if($i < 3) {
if(!empty($_FILES['filecontainer']['name'][$key])) {
$var2->uploadfile($key,$error);
if($var2->errorid == 0) {
$file['id'][]=$var2->fileid;
$file['name'][]=urlencode($_FILES['filecontainer']['name'][$key]);
} else $err[]=$var2->errorid; }} $i=$i+1; }
unset($var2);
$i=1; $ref="";
$file['id']=array_unique($file['id']);
foreach($file['id'] as $key=>$value) {
if($ref == "") { $ref="?file" .$i. "=" .$value. "&filen" .$i. "=" .$file['name'][$key]; } else { $ref.="&file" .$i. "=" .$value. "&filen" .$i. "=" .$file['name'][$key]; }
$i=$i+1; }
$ref="print.php" .$ref;
if(count($err) == 0) {
if(count($file['id']) == 0)
setcookie("perr",'9',time()+3,'/','.orntel.com',false,true);
else {
if($var->add_task($file['id'])) {
header('Location: account.php?show=history');
exit;
}}} else {
foreach($err as $key=>$value)
setcookie("perr" .$key,$value,time()+3,'/','.orntel.com',false,true);
}
header('Location: ' . $ref);
exit;
} elseif($bname == "account") {
if(!empty($_POST['fileid']) && strlen($_POST['fileid']) == 32) {
if(empty($_POST['process'])) {
header('Location: account.php');
exit;
}
if($_POST['process'] == 1)
$p=1;
elseif($_POST['process'] == 2)
$p=2;
else {
header('Location: account.php');
exit;
}
$con=new dbconnect("db_printhub");
$con->q_exe("select fileid from pdf_files where fileid = :fileid and userid = :userid limit 1;", array('fileid'=>$_POST['fileid'], 'userid'=>$userid));
$rows=count($con->rows);
unset($con);
if($rows == 1) {
if($p == 1) {
setcookie("fileid",$_POST['fileid'],0,'/','.orntel.com',false,true);
header('Location: predefined.php');
exit;
} else {
$var = new predefined;
$var->fileid=$_POST['fileid'];
$var->delete_predefined();
}}} elseif(!empty($_POST['mid']) && preg_match('/^[0-9]+$/i', $_POST['mid'])) {
$var=new mailbox($userid,1);
$var->delete($_POST['mid']);
header('Location: account.php?show=mail');
exit;
} elseif(!empty($_POST['restart']) && $_POST['restart'] == 1) {
$var=new restart_task($userid,$_POST['pid']);
if($var->errorid != 0)
setcookie("maerr",$var->errorid,time()+3,'/','.orntel.com',false,true);
else {
$var->allow_restart();
setcookie("maerr",$var->errorid,time()+3,'/','.orntel.com',false,true);
}
header('Location: account.php?show=history');
exit;
} elseif(!empty($_POST['reprint']) && $_POST['reprint'] == 1) {
if(!empty($_POST['reason']) && strlen($_POST['reason']) > 5 && preg_match('/^[a-z0-9 ,._\/-]+$/i', $_POST['reason']) && !empty($_POST['pid']) && strlen($_POST['pid'])==10 && preg_match('/^[0-9]+$/i', $_POST['pid'])) {
$var=new reprint_task($userid,$_POST['pid']);
if($var->errorid != 0)
setcookie("maerr",$var->errorid,time()+3,'/','.orntel.com',false,true);
else {
$var->reprint();
setcookie("maerr",$var->errorid,time()+3,'/','.orntel.com',false,true);
}
} else
setcookie("maerr",4,time()+3,'/','.orntel.com',false,true);
header('Location: account.php?show=history');
exit;
}
header('Location: account.php');
exit;
}
} else {
setcookie('_error','2',time()+3,'/','.orntel.com',false,true);
header('Location: http://my.orntel.com/login.php');
exit;
}
?>