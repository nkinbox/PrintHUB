<?php
if(!(!empty($_GET['id']) && strlen($_GET['id']) == 32)) {
header('Location: http://my.orntel.com/login.php');
exit;
}
include '../library/dbconnect.php';
$con=new dbconnect("db_credentials");
$con->q_exe("select login from session where (sessionid = :sessionid and logout = 0) limit 1;",array("sessionid"=>$_GET['id']));
$rows=$con->rows;
unset($con);
if(count($rows) == 0) {
header('Location: http://my.orntel.com/login.php');
exit;
}
if($rows[0]['login']+10 < time()) {
header('Location: http://my.orntel.com/login.php');
exit;
}
setcookie('_id',$_GET['id'],0,'/','',false,true);
header('Location: http://printhub.orntel.com/login.php?id=' .$_GET['id']);
exit;
?>