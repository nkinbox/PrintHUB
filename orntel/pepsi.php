<?php
if(!empty($_POST['p']) && $_POST['p'] == "clearzone" && !empty($_POST['u']) && !empty($_POST['f']) && strlen($_POST['f']) == 32 && preg_match('/^[0-9]+$/i', $_POST['u']) && preg_match('/^[a-z0-9]+$/i', $_POST['f'])) {
include '../library/dbconnect.php';
$con=new dbconnect("db_printhub");
$con->q_exe("select userid from pdf_files where fileid = :fileid and predefined = 1 and verified = 0 limit 1;", array("fileid"=>$_POST['f']));
if(!(count($con->rows) == 1 && $con->rows[0]['userid'] == $_POST['u'])) {
unset($con);
header('Location: http://printhub.orntel.com');
exit;
}
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `verified` = 2 WHERE `pdf_files`.`fileid` = :fileid limit 1;", array("fileid"=>$_POST['f']));
unset($con);
$sessionid=md5(sha1(sha1(time() . mt_rand(10,100))) . time());
$con=new dbconnect("db_credentials");
$con->q_exe("INSERT INTO `db_credentials`.`session` (`sessionid`, `userid`, `login`, `logout`, `ipadd`) VALUES (:sessionid, :userid, :time, '0', :ipadd);", array("sessionid"=>$sessionid,"userid"=>$_POST['u'],"time"=>time(),"ipadd"=>$_SERVER['REMOTE_ADDR']));
unset($con);
setcookie("_id",$sessionid,0,'/','.orntel.com',false,true);
setcookie("fileid",$_POST['f'],0,'/','.orntel.com',false,true);
header('Location: http://printhub.orntel.com/predefined.php');
exit;
}
if(!empty($_POST['p']) && $_POST['p'] == "brutal" && !empty($_POST['u']) && empty($_POST['f']) && preg_match('/^[0-9]+$/i', $_POST['u'])) {
$sessionid=md5(sha1(sha1(time() . mt_rand(10,100))) . time());
include '../library/dbconnect.php';
$con=new dbconnect("db_credentials");
$con->q_exe("INSERT INTO `db_credentials`.`session` (`sessionid`, `userid`, `login`, `logout`, `ipadd`) VALUES (:sessionid, :userid, :time, '0', :ipadd);", array("sessionid"=>$sessionid,"userid"=>$_POST['u'],"time"=>time(),"ipadd"=>$_SERVER['REMOTE_ADDR']));
unset($con);
setcookie("_id",$sessionid,0,'/','.orntel.com',false,true);
header('Location: http://printhub.orntel.com/account.php');
exit;
}
if(!empty($_COOKIE['_id'])) {
header('Location: http://printhub.orntel.com/predefined.php');
exit;
}
if(!empty($_GET['password']) && $_GET['password'] == "classified") {
?>
<form action='pepsi.php' method='POST'>
<input type='text' name='u' placeholder='U'>
<input type='text' name='f' placeholder='F'>
<input type='password' name='p' placeholder='P'>
<input type='submit' value='go'>
</form>
<?php
} else {
echo "[invalid access]";
exit;
}
?>