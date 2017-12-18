<?php
include 'dbconnect.php';
function create_t($db,$table,$sql) {
$con = new dbconnect($db);
if(!$con->err)
$sq_l ="select 1 from `" .$table. "`";
$con->q_exe($sq_l,array());
if($con->err) {
$con->q_run($sql);
if(!$con->err)
echo $table . " : Table created.<br>";
else echo "Error occured" . $con->errmsg . "<br>";
}
else echo $table . " : Already exists<br>";
}

/*_____________________________________________________________*/


$db = "db_manage";
$table = "handlers";
$sql = "CREATE TABLE handlers (
sno INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
username CHAR(10) NOT NULL,
password CHAR(10) NOT NULL,
sessionid CHAR(32) NOT NULL,
priv CHAR(4) NOT NULL
)";
create_t($db,$table,$sql);


$db = "db_credentials";

$table = "credits";
$sql = "CREATE TABLE credits (
userid INT(11) UNSIGNED PRIMARY KEY,
coins SMALLINT(5) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);

$table = "free_credits";
$sql = "CREATE TABLE free_credits (
userid INT(11) UNSIGNED PRIMARY KEY,
coins SMALLINT(5) UNSIGNED NOT NULL,
time INT(11) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);

$table = "session";
$sql = "CREATE TABLE session (
sessionid CHAR(32) PRIMARY KEY,
userid INT(11) UNSIGNED NOT NULL,
login INT(11) UNSIGNED NOT NULL,
logout INT(11) UNSIGNED NOT NULL,
ipadd VARCHAR(45) NOT NULL
)";
create_t($db,$table,$sql);


$table = "account";
$sql = "CREATE TABLE account (
userid INT(11) UNSIGNED PRIMARY KEY,
username CHAR(10) NOT NULL,
phone CHAR(13) NOT NULL,
altphone CHAR(13) NOT NULL,
password CHAR(10) NOT NULL,
answer CHAR(10) NOT NULL,
maxlogin TINYINT(6) UNSIGNED NOT NULL,
maxduration MEDIUMINT(9) UNSIGNED NOT NULL,
active TINYINT(6) UNSIGNED NOT NULL,
blocked INT(11) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);


$db = "db_user";

$table = "personal";
$sql = "CREATE TABLE personal (
userid INT(11) UNSIGNED PRIMARY KEY,
username VARCHAR(30) NOT NULL,
name VARCHAR(30) NOT NULL,
bday CHAR(10) NOT NULL,
email VARCHAR(30) NOT NULL,
phone CHAR(13) NOT NULL,
sex CHAR(1) NOT NULL,
college VARCHAR(50)
)";
create_t($db,$table,$sql);


$table = "prepayment";
$sql = "CREATE TABLE prepayment (
userid INT(11) UNSIGNED PRIMARY KEY,
name VARCHAR(30) NOT NULL,
address VARCHAR(50) NOT NULL,
city VARCHAR(30) NOT NULL,
state VARCHAR(25) NOT NULL,
pin VARCHAR(10) NOT NULL,
country VARCHAR(10) NOT NULL,
phone VARCHAR(13) NOT NULL,
email VARCHAR(30) NOT NULL
)";
create_t($db,$table,$sql);



$db = "db_counter";

$table = "cash_counter";
$sql = "CREATE TABLE cash_counter (
orderid INT(11) UNSIGNED PRIMARY KEY,
userid INT(11) UNSIGNED NOT NULL,
status TINYINT(6) UNSIGNED NOT NULL,
amount INT(11) UNSIGNED NOT NULL,
response TEXT,
req_time INT(11) UNSIGNED NOT NULL,
res_time INT(11) UNSIGNED NOT NULL,
lab TINYINT(6) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);

$table = "debit_counter";
$sql = "CREATE TABLE debit_counter (
sno INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
userid INT(11) UNSIGNED NOT NULL,
pid CHAR(10) NOT NULL,
amount INT(11) UNSIGNED NOT NULL,
time INT(11) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);


$table = "mailcontainer";
$sql = "CREATE TABLE mailcontainer (
mid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
userid INT(11) UNSIGNED NOT NULL,
showin TINYINT(6) UNSIGNED NOT NULL,
subject VARCHAR(20) NOT NULL,
content VARCHAR(160) NOT NULL,
unread TINYINT(6) UNSIGNED NOT NULL,
deleted TINYINT(6) UNSIGNED NOT NULL,
time INT(11) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);


$table = "msgcounter";
$sql = "CREATE TABLE msgcounter (
mid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
phone CHAR(13) NOT NULL,
template TINYINT(6) UNSIGNED NOT NULL,
value VARCHAR(10) NOT NULL,
time INT(11) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);

$table = "spamcounter";
$sql = "CREATE TABLE spamcounter (
bid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
phone CHAR(13) NOT NULL,
time INT(11) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);


$table = "print_task";
$sql = "CREATE TABLE print_task (
sno INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
pid CHAR(10) NOT NULL,
fileid CHAR(32) NOT NULL,
userid INT(11) UNSIGNED NOT NULL,
prnid INT(11) UNSIGNED NOT NULL,
copies TINYINT(6) UNSIGNED NOT NULL,
pages INT(11) UNSIGNED NOT NULL,
bw TINYINT(6) UNSIGNED NOT NULL,
free TINYINT(6) UNSIGNED NOT NULL,
interface TINYINT(6) UNSIGNED NOT NULL,
stage TINYINT(6) UNSIGNED NOT NULL,
reprint TINYINT(6) UNSIGNED NOT NULL,
time INT(11) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);



$table = "reprint_task";
$sql = "CREATE TABLE reprint_task (
sno INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
pid CHAR(10) NOT NULL,
userid INT(11) UNSIGNED NOT NULL,
reason VARCHAR(255) NOT NULL,
reprinted TINYINT(6) UNSIGNED NOT NULL,
time INT(11) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);




$db = "db_printhub";

$table = "pdf_files";
$sql = "CREATE TABLE pdf_files (
fileid CHAR(32) PRIMARY KEY,
filename CHAR(32) NOT NULL,
userid INT(11) UNSIGNED NOT NULL,
time INT(11) UNSIGNED NOT NULL,
predefined TINYINT(6) UNSIGNED NOT NULL,
linked TINYINT(6) UNSIGNED NOT NULL,
verified TINYINT(6) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);


$table = "tags";
$sql = "CREATE TABLE tags (
tid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
cid INT(11) UNSIGNED NOT NULL,
category CHAR(3) NOT NULL,
name CHAR(15) NOT NULL
)";
create_t($db,$table,$sql);


$table = "tags_container";
$sql = "CREATE TABLE tags_container (
fileid CHAR(32) NOT NULL,
tid INT(11) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);


$table = "predefined";
$sql = "CREATE TABLE predefined (
fileid CHAR(32) PRIMARY KEY,
pages INT(11) UNSIGNED NOT NULL,
name VARCHAR(20) NOT NULL,
description VARCHAR(50) NOT NULL,
uploader VARCHAR(30) NOT NULL,
time INT(11) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);


$table = "printers";
$sql = "CREATE TABLE printers (
pid INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name CHAR(12) NOT NULL,
username CHAR(10) NOT NULL,
password CHAR(10) NOT NULL,
model VARCHAR(30) NOT NULL,
location VARCHAR(30) NOT NULL,
map VARCHAR(30) NOT NULL,
status TINYINT(6) UNSIGNED NOT NULL,
time INT(11) UNSIGNED NOT NULL
)";
create_t($db,$table,$sql);

?>