<?php
class account {
private $phone;
private $code;
private $name;
private $username;
private $password;
private $userid;
public $errorid=0;
public $attempt;

private function add_to_spam() {
if($this->attempt>5) {
$con=new dbconnect("db_counter");
$con->q_exe("INSERT INTO `db_counter`.`spamcounter` (`bid`, `phone`, `time`) VALUES ('0', :phone, :time);", array('phone'=>$this->phone, 'time'=>time()));
unset($con);
$this->reset();
return 7;
}
else return 0;
}

public function add_user() {
$con=new dbconnect("db_credentials");
$con->q_exe("select userid from account order by userid desc limit 1;",array());
if(count($con->rows) == 0)
$this->userid = 1;
else
$this->userid=$con->rows[0]['userid']+1;
$con->q_exe("INSERT INTO `db_credentials`.`account` (`userid`, `username`, `phone`, `altphone`, `password`, `answer`, `maxlogin`, `maxduration`, `active`, `blocked`) VALUES (:userid, :username, :phone, '0', :password, '0', '2', '7200', '1', '0');",array("userid"=>$this->userid,"username"=>substr(md5(sha1($this->username)),0,10),"phone"=>$this->phone,"password"=>$this->password));
$con->q_exe("INSERT INTO `db_credentials`.`credits` (`userid`, `coins`) VALUES (:userid, '0');",array("userid"=>$this->userid));
$con->q_exe("INSERT INTO `db_credentials`.`free_credits` (`userid`, `coins`, `time`) VALUES (:userid, '20', :time);",array("userid"=>$this->userid,"time"=>time()));
unset($con);
$con=new dbconnect("db_user");
$con->q_exe("INSERT INTO `db_user`.`personal` (`userid`, `username`, `name`, `bday`, `email`, `phone`, `sex`, `college`) VALUES (:userid, :username, :name, '01 jan 90', '0', :phone, '0', '0');",array("userid"=>$this->userid,"username"=>$this->username,"name"=>$this->name,"phone"=>$this->phone));
$con->q_exe("INSERT INTO `db_user`.`prepayment` (`userid`, `name`, `address`, `city`, `state`, `pin`, `country`, `phone`, `email`) VALUES (:userid, :name, '0', '0', '0', '0', 'India', :phone, '0');",array("userid"=>$this->userid,"name"=>$this->name,"phone"=>$this->phone));
unset($con);
send_msg($this->phone,5,$this->username);
$this->reset();
$this->cookie('un',$this->username);
}

private function check_phone() {
$con=new dbconnect("db_counter");
$con->q_exe("select bid from `spamcounter` where phone = :phone limit 1;", array('phone'=>'+91' . $_POST['phone']));
$rows=count($con->rows);
unset($con);
if($rows>0)
return 7;
$con=new dbconnect("db_credentials");
$con->q_exe("select userid from account where phone = :phone or altphone = :phone limit 1;", array('phone'=>'+91' . $_POST['phone']));
$rows=count($con->rows);
unset($con);
if($rows>0)
return 10;
return 0;
}

private function check_username() {
$con = new dbconnect("db_credentials");
$con->q_exe("select userid from account where username = :username limit 1;", array('username' => substr(md5(sha1($_POST['username'])),0,10)));
$rows=count($con->rows);
unset($con);
if($rows == 0)
return 0;
else
return 4;
}

public function cookie($name,$value,$do='set') {
if($do == 'set')
$time=0;
else
$time=time()-43200;
if($name == "err")
$time=time()+3;
setcookie($name,$value,$time,'/','.orntel.com',false,true);
}

private function count_attempt() {
if(empty($_COOKIE['_at'])) {
$this->cookie('_at','1');
$this->attempt=1;
} else {
$this->cookie('_at',$_COOKIE['_at']+1);
$this->attempt=$_COOKIE['_at']+1;
}}

public function reset() {
$this->cookie('_na','0','unset');
$this->cookie('_ph','0','unset');
$this->cookie('_un','0','unset');
$this->cookie('_co','0','unset');
$this->cookie('_st','0','unset');
$this->cookie('_at','0','unset');
$this->cookie('err','0','unset');
}

private function send_code() {
$code=mt_rand(1111,9999);
$this->code=substr(md5(sha1($code)),0,10);
$con=new dbconnect("db_counter");
$con->q_exe("select time from msgcounter where phone = :phone order by time desc limit 3;", array('phone'=>$this->phone));
$rows=$con->rows;
unset($con);
if(count($rows)==3 && (time() - $rows[2]['time']) < 300)
return 8;
else {
send_msg($this->phone,1,$code); return 0;}
}

public function set_code() {
if(!empty($_POST['verify']) && strlen($_POST['verify'])==4 && preg_match('/^[0-9]+$/i', $_POST['verify'])) {
$this->code=$_POST['verify'];
$this->count_attempt();
$this->errorid=$this->add_to_spam();
if($this->errorid == 0) {
$this->errorid=$this->verify_code('post');
if($this->errorid == 0)
$this->cookie("_co",$this->code);
}}
elseif(!empty($this->password) && !empty($_COOKIE['_co']) && strlen($_COOKIE['_co'])==4 && preg_match('/^[0-9]+$/i', $_COOKIE['_co'])) {
$this->code=$_COOKIE['_co'];
$this->errorid=$this->verify_code('cookie');
} else $this->errorid=2;
}

public function set_password() {
if(!empty($_POST['password']) && strlen($_POST['password'])>6) {
if(!empty($_POST['cpassword']) && $_POST['password']==$_POST['cpassword']) {
$this->password=substr(md5(sha1(strtolower($_POST['password']))),0,10);
} else $this->errorid=6;
} else $this->errorid=5;
}

public function set_phone() {
if(!empty($_POST['phone']) && strlen($_POST['phone'])==10 && preg_match('/^[0-9]+$/i', $_POST['phone'])) {
$this->errorid=$this->check_phone();
if($this->errorid == 0) {
$this->phone="+91" . $_POST['phone'];
$this->errorid=$this->send_code();
if($this->errorid == 0)
$this->cookie("_ph",$this->phone);
}} elseif(!empty($_COOKIE['_ph']) && strlen($_COOKIE['_ph'])==13 && preg_match('/^[0-9+]+$/i', $_COOKIE['_ph']))
$this->phone=$_COOKIE['_ph'];
else $this->errorid=1;
}

public function set_name() {
if(!empty($_POST['name']) && strlen($_POST['name'])>4 && strlen($_POST['name'])<30 && preg_match('/^[a-z ]+[ ]+[a-z]+$/i', $_POST['name']))
$this->name=$_POST['name'];
else $this->errorid=3;
if(!empty($_POST['name']))
$this->cookie("_na",$_POST['name']);
}

public function set_username() {
if(!empty($_POST['username']) && strlen($_POST['username'])>4 && strlen($_POST['username'])<30 && preg_match('/^[a-z0-9 .]+$/i', $_POST['username'])) {
$this->errorid=$this->check_username();
if($this->errorid == 0)
$this->username=$_POST['username'];
} else $this->errorid=4;
if(!empty($_POST['username']))
$this->cookie("_un",$_POST['username']);
}

private function verify_code($by) {
$con = new dbconnect("db_counter");
$con->q_exe("select value from msgcounter where phone = :phone order by time desc limit 1;", array('phone'=>$this->phone));
$row = $con->rows[0]['value'];
unset($con);
if($row == substr(md5(sha1($this->code)),0,10))
return 0;
else {
if($by == 'cookie')
return 9;
return 2;
}}
}

class adv_setting {
public $userid;
public $altphone;
public $answer;
public $maxlogin;
public $maxduration;
public $active;
public $errorid=array();

public function extractdata() {
$con=new dbconnect("db_credentials");
$con->q_exe("select altphone, answer, maxlogin, maxduration, active from account where userid = :userid limit 1;",array("userid"=>$this->userid));
$rows=$con->rows;
unset($con);
$this->altphone=$rows[0]['altphone'];
if($rows[0]['answer'] == "0")
$this->answer="not set";
else
$this->answer="Secret word";
switch($rows[0]['maxduration']) {
case 3600:
$this->maxduration=1;
break;
case 7200:
$this->maxduration=2;
break;
case 43200:
$this->maxduration=12;
break;
case 86400:
$this->maxduration=24;
break;
default:
$this->maxduration=2;
}
$this->maxlogin=$rows[0]['maxlogin'];
$this->active=$rows[0]['active'];
}

public function editdata() {
$this->extractdata();
$edits="";
if(!empty($_POST['altphone'])) {
if($this->altphone != $_POST['altphone']) {
$_POST['altphone']=substr($_POST['altphone'],3);
if(strlen($_POST['altphone'])==10 && preg_match('/^[0-9]+$/i', $_POST['altphone'])) {
$_POST['altphone']="+91" . $_POST['altphone'];
if($edits == "")
$edits="`altphone` = '" .$_POST['altphone']. "'";
else
$edits.=", `altphone` = '" .$_POST['altphone']. "'";
} else $this->errorid[] = "11";
}}
if(!empty($_POST['answer'])) {
if($edits == "")
$edits="`answer` = '" .substr(md5(sha1($_POST['answer'])),0,10). "'";
else
$edits.=", `answer` = '" .substr(md5(sha1($_POST['answer'])),0,10). "'";
}
if(!empty($_POST['maxlogin'])) {
if($this->maxlogin != $_POST['maxlogin']) {
switch($_POST['maxlogin']) {
case 1:
$m=1;
break;
case 2:
$m=2;
break;
case 3:
$m=3;
break;
case 4:
$m=4;
break;
case 5:
$m=5;
break;
default:
$m=2;
}
if($edits == "")
$edits="`maxlogin` = '" .$m. "'";
else
$edits.=", `maxlogin` = '" .$m. "'";
}}
if(!empty($_POST['maxduration'])) {
if($this->maxduration != $_POST['maxduration']) {
switch($_POST['maxduration']) {
case 1:
$m=3600;
break;
case 2:
$m=7200;
break;
case 12:
$m=43200;
break;
case 24:
$m=86400;
break;
default:
$m=7200;
}
if($edits == "")
$edits="`maxduration` = '" .$m. "'";
else
$edits.=", `maxduration` = '" .$m. "'";
}}
if(!empty($_POST['active'])) {
if($_POST['active'] == 'd') {
if($edits == "")
$edits="`active` = '0'";
else
$edits.=", `active` = '0'";
}}
if($edits != "") {
$sql="UPDATE `db_credentials`.`account` SET " .$edits. " WHERE `account`.`userid` = " .$this->userid. " limit 1;";
$con=new dbconnect("db_user");
$con->q_run($sql);
unset($con);
}}
}

class cashier {
private $userid;
public $coins;
public $points;
private $phone;

function __construct($userid) {
$this->userid=$userid;
}

public function credit($amount,$from) {
if($from=="free") {
$this->getpoints();
$this->points=$this->points+$amount;
$con=new dbconnect("db_credentials");
$con->q_exe("UPDATE `db_credentials`.`free_credits` SET `coins` = :coins WHERE `free_credits`.`userid` = :userid limit 1;", array('coins'=>$this->points,'userid'=>$this->userid));
unset($con);
} else {
$this->getphone();
$this->getcoins();
$this->coins=$this->coins+$amount;
$con=new dbconnect("db_credentials");
$con->q_exe("UPDATE `db_credentials`.`credits` SET `coins` = :coins WHERE `credits`.`userid` = :userid limit 1;", array('coins'=>$this->coins,'userid'=>$this->userid));
unset($con);
$this->getcoins();
send_msg($this->phone, 8, $amount, $this->coins);
}}

public function debit($amount,$from) {
if($from == "free") {
$this->getpoints();
if($this->points >= $amount) {
$this->points=$this->points-$amount;
$con=new dbconnect("db_credentials");
$con->q_exe("UPDATE `db_credentials`.`free_credits` SET `coins` = :coins WHERE `free_credits`.`userid` = :userid limit 1;", array('coins'=>$this->points,'userid'=>$this->userid));
unset($con);
return true;
} else {
$amount=$amount-$this->points;
$this->points=0;
$this->getcoins();
if($this->coins >= $amount) {
$this->coins=$this->coins-$amount;
$con=new dbconnect("db_credentials");
$con->q_exe("UPDATE `db_credentials`.`free_credits` SET `coins` = :coins WHERE `free_credits`.`userid` = :userid limit 1;", array('coins'=>$this->points,'userid'=>$this->userid));
$con->q_exe("UPDATE `db_credentials`.`credits` SET `coins` = :coins WHERE `credits`.`userid` = :userid limit 1;", array('coins'=>$this->coins,'userid'=>$this->userid));
unset($con);
$this->getphone();
send_msg($this->phone, 7, $amount, $this->coins);
return true;
} else return false;
}} else {
$this->getphone();
$this->getcoins();
if($this->coins >= $amount) {
$this->coins=$this->coins-$amount;
$con=new dbconnect("db_credentials");
$con->q_exe("UPDATE `db_credentials`.`credits` SET `coins` = :coins WHERE `credits`.`userid` = :userid limit 1;", array('coins'=>$this->coins,'userid'=>$this->userid));
unset($con);
send_msg($this->phone, 7, $amount, $this->coins);
return true;
} else {
$amount=$amount-$this->coins;
$amount2=$this->coins;
$this->coins=0;
$this->getpoints();
if($this->points >= $amount) {
$this->points=$this->points-$amount;
$con=new dbconnect("db_credentials");
$con->q_exe("UPDATE `db_credentials`.`free_credits` SET `coins` = :coins WHERE `free_credits`.`userid` = :userid limit 1;", array('coins'=>$this->points,'userid'=>$this->userid));
$con->q_exe("UPDATE `db_credentials`.`credits` SET `coins` = :coins WHERE `credits`.`userid` = :userid limit 1;", array('coins'=>$this->coins,'userid'=>$this->userid));
unset($con);
send_msg($this->phone, 7, $amount2, $this->coins);
return true;
} else return false;
}}
}
private function getphone() {
$con=new dbconnect("db_credentials");
$con->q_exe("select phone from account where userid = :userid limit 1;", array('userid'=>$this->userid));
$this->phone=$con->rows[0]['phone'];
unset($con);
}
public function getcoins() {
$con=new dbconnect("db_credentials");
$con->q_exe("select coins from credits where userid = :userid limit 1;", array('userid'=>$this->userid));
$this->coins=$con->rows[0]['coins'];
unset($con);
}

public function getpoints() {
$con=new dbconnect("db_credentials");
$con->q_exe("select coins, time from free_credits where userid = :userid limit 1;", array('userid'=>$this->userid));
$time=$con->rows[0]['time']+604800;
if($time>time()) {
$this->points=$con->rows[0]['coins'];
unset($con);
} else {
$this->points=20;
$con->q_exe("UPDATE `db_credentials`.`free_credits` SET `coins` = :coins, `time` = :time WHERE `free_credits`.`userid` = :userid limit 1;", array('coins'=>$this->points,'time'=>time(),'userid'=>$this->userid));
unset($con);
}}
}

class ccavenue extends prepayment {
private $merchant_id="41385";
private $working_key="58C19D70005BAD89D30D240274E4FDCE";
public $access_code="AVPC02BG00BS75CPSB";
private $url="http://www.orntel.com/ccavResponse.php";
public $encrypted_data;
public $decrypted_data;
public $response_data=array();
private $order_id;
public $amount;
public $status;

function __construct($userid,$orderid,$amount) {
$this->userid=$userid;
$this->order_id=$orderid;
$this->amount=$amount;
}

public function request() {
$con=new dbconnect("db_counter");
$con->q_exe("INSERT INTO `db_counter`.`cash_counter` (`orderid`, `userid`, `status`, `amount`, `response`, `req_time`, `res_time`, `lab`) VALUES (:orderid, :userid, '0', '0', '0', :time, '0', '0');", array("orderid"=>$this->order_id,"userid"=>$this->userid,"time"=>time()));
unset($con);
$this->decrypted_data="merchant_id=" .$this->merchant_id. "&order_id=" .$this->order_id. "&amount=" .$this->amount. "&currency=INR&redirect_url=" .$this->url. "&cancel_url=" .$this->url. "&language=EN";
$this->extractdata();
$arr=array("name","address","city","state","pin","country","phone","email");
foreach($arr as $v) {
if($this->$v != "0") {
$va="";
switch($v) {
case "pin":
$va="&billing_zip=" .$this->$v;
break;
case "phone":
$va="&billing_tel=" .substr($this->$v,3,10);
break;
default:
$va="&billing_" .$v. "=" .$this->$v;
}
$this->decrypted_data=$this->decrypted_data.$va;
}}
$this->encrypted_data=$this->encrypt($this->decrypted_data,$this->working_key);
}

public function response() {
$this->encrypted_data=$_POST["encResp"];
$this->decrypted_data=$this->decrypt($this->encrypted_data,$this->working_key);
$arr=explode('&', $this->decrypted_data);
$response="";
foreach($arr as $val) {
$v=explode('=', $val);
$this->response_data[$v[0]]=$v[1];
if($response == "")
$response=$v[0]. "=>" .$v[1]. "<br>";
else
$response.=$v[0]. "=>" .$v[1]. "<br>";
}
$this->order_id=$this->response_data['order_id'];
$this->amount=$this->response_data['amount'];
switch($this->response_data['order_status']) {
case "Success":
$this->status=1;
break;
case "Failure":
$this->status=2;
break;
case "Aborted":
$this->status=3;
break;
case "Invalid":
$this->status=4;
}
$con=new dbconnect("db_counter");
$con->q_exe("select userid from cash_counter where orderid = :orderid limit 1;",array("orderid"=>$this->order_id));
$this->userid=$con->rows[0]['userid'];
$con->q_exe("UPDATE  `db_counter`.`cash_counter` SET  `status` = :status, `amount` = :amount, `response` = :response, `res_time` =  :time WHERE  `cash_counter`.`orderid` = :orderid limit 1;", array("status"=>$this->status,"amount"=>$this->amount,"response"=>$response,"time"=>time(),"orderid"=>$this->order_id));
unset($con);
$var=new mailbox($this->userid,1);
switch($this->status) {
case 1:
$v=new cashier($this->userid);
$v->credit(intval($this->amount),"paid");
$var->subject="Payment Successful";
$var->content="Payment of " .$this->amount. " INR was made successfully to Orntel. Order Id is " .$this->order_id. ". Your Account has been Credited.";
break;
case 2:
$var->subject="Payment Failed";
$var->content="Payment to Orntel Failed. Transaction Id " .$this->order_id;
break;
case 3:
$var->subject="Payment Aborted";
$var->content="Payment to Orntel was Aborted. Transaction Id " .$this->order_id;
break;
case 4:
$var->subject="Invalid Payment";
$var->content="An Invalid Payment error occured. Transaction Id " .$this->order_id;
break;
}
$var->send();
}

private function encrypt($plainText,$key) {
$secretKey = $this->hextobin(md5($key));
$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
$openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
$blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'cbc');
$plainPad = $this->pkcs5_pad($plainText, $blockSize);
if (mcrypt_generic_init($openMode, $secretKey, $initVector) != -1) {
$encryptedText = mcrypt_generic($openMode, $plainPad);
mcrypt_generic_deinit($openMode);
}
return bin2hex($encryptedText);
}

private function decrypt($encryptedText,$key) {
$secretKey = $this->hextobin(md5($key));
$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
$encryptedText=$this->hextobin($encryptedText);
$openMode = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '','cbc', '');
mcrypt_generic_init($openMode, $secretKey, $initVector);
$decryptedText = mdecrypt_generic($openMode, $encryptedText);
$decryptedText = rtrim($decryptedText, "\0");
mcrypt_generic_deinit($openMode);
return $decryptedText;
}

private function pkcs5_pad($plainText, $blockSize) {
$pad = $blockSize - (strlen($plainText) % $blockSize);
return $plainText . str_repeat(chr($pad), $pad);
}

private function hextobin($hexString) {
$length = strlen($hexString);
$binString="";
$count=0;
while($count<$length) {
$subString =substr($hexString,$count,2);
$packedString = pack("H*",$subString);
if($count==0)
$binString=$packedString;
else
$binString.=$packedString;
$count+=2;
}
return $binString;
}
}

class mailbox {
private $mid;
private $userid;
private $showin;
public $subject="none";
public $content="none";

function __construct($userid, $showin) {
$this->userid=$userid;
$this->showin=$showin;
}

public function send() {
$con=new dbconnect("db_counter");
$con->q_exe("INSERT INTO `db_counter`.`mailcontainer` (`mid`, `userid`, `showin`, `subject`, `content`, `unread`, `deleted`, `time`) VALUES (0, :userid, :showin, :subject, :content, 1, 0, :time);", array('userid'=>$this->userid,"showin"=>$this->showin,"subject"=>$this->subject,"content"=>$this->content,"time"=>time()));
unset($con);
$con=new dbconnect("db_credentials");
$con->q_exe("select phone from account where userid = :userid limit 1;", array('userid'=>$this->userid));
$phone=$con->rows[0]['phone'];
unset($con);
send_msg($phone,4);
}

public function delete($mid) {
$con=new dbconnect("db_counter");
$con->q_exe("UPDATE `db_counter`.`mailcontainer` SET `deleted` = 1 WHERE `mailcontainer`.`mid` = :mid and  `mailcontainer`.`userid` = :userid limit 1;", array("mid"=>$mid, "userid"=>$this->userid));
unset($con);
}

public function countmail() {
$con=new dbconnect("db_counter");
if($this->showin == 0) {
$con->q_exe("select count(unread) as mails from mailcontainer where userid = :userid and unread = 1;", array("userid"=>$this->userid));
} else {
$con->q_exe("select count(unread) as mails from mailcontainer where userid = :userid and showin = :showin and unread = 1;", array("userid"=>$this->userid,"showin"=>$this->showin));
}
$r=$con->rows[0]['mails'];
unset($con);
return $r;
}

public function showmail() {
$con=new dbconnect("db_counter");
if($this->showin == 0) {
$con->q_exe("select mid, subject, content, time from mailcontainer where userid = :userid and deleted = 0 order by time desc limit 5;", array("userid"=>$this->userid));
$con->q_exe("UPDATE `db_counter`.`mailcontainer` SET `unread` = 0 WHERE `mailcontainer`.`userid` = :userid and `mailcontainer`.`unread` = 1;", array("userid"=>$this->userid));
} else {
$con->q_exe("UPDATE `db_counter`.`mailcontainer` SET `unread` = 0 WHERE `mailcontainer`.`userid` = :userid and `mailcontainer`.`showin` = :showin and `mailcontainer`.`unread` = 1;", array("userid"=>$this->userid,"showin"=>$this->showin));
$con->q_exe("select mid, subject, content, time from mailcontainer where userid = :userid and showin = :showin and deleted = 0 order by time desc limit 5;", array("userid"=>$this->userid,"showin"=>$this->showin));
}
$rows=$con->rows;
unset($con);
return $rows;}
}

class prepayment {
public $userid;
public $name;
public $address;
public $city;
public $state;
public $pin;
public $country;
public $phone;
public $email;

public function extractdata() {
$con=new dbconnect("db_user");
$con->q_exe("select * from prepayment where userid = :userid limit 1;", array("userid"=>$this->userid));
$rows=$con->rows;
unset($con);
$this->name=$rows[0]['name'];
$this->address=$rows[0]['address'];
$this->city=$rows[0]['city'];
$this->state=$rows[0]['state'];
$this->pin=$rows[0]['pin'];
$this->country=$rows[0]['country'];
$this->phone=$rows[0]['phone'];
$this->email=$rows[0]['email'];
}

public function editdata() {
$this->extractdata();
if(!empty($_POST['name'])) {
if($this->name != $_POST['name']) {
if(strlen($_POST['name'])>4 && strlen($_POST['name'])<30 && preg_match('/^[a-z ]+[ ]+[a-z]+$/i', $_POST['name']))
$edits="`name` = '" .$_POST['name']. "'";
else $this->errorid[] = "1";
}}
if(!empty($_POST['address'])) {
if($this->address != $_POST['address']) {
if(strlen($_POST['address'])>4 && strlen($_POST['address'])<50 && preg_match('/^[a-z0-9 ,._\/-]+$/i', $_POST['address'])) {
if($edits == "")
$edits="`address` = '" .$_POST['address']. "'";
else
$edits.=", `address` = '" .$_POST['address']. "'";
} else $this->errorid[] = "6";
}}
if(!empty($_POST['city'])) {
if($this->city != $_POST['city']) {
if(strlen($_POST['city'])>4 && strlen($_POST['city'])<30 && preg_match('/^[a-z0-9 ._\/-]+$/i', $_POST['city'])) {
if($edits == "")
$edits="`city` = '" .$_POST['city']. "'";
else
$edits.=", `city` = '" .$_POST['city']. "'";
} else $this->errorid[] = "7";
}}
if(!empty($_POST['state'])) {
if($this->state != $_POST['state']) {
if(strlen($_POST['state'])>4 && strlen($_POST['state'])<25 && preg_match('/^[a-z0-9 ._\/-]+$/i', $_POST['state'])) {
if($edits == "")
$edits="`state` = '" .$_POST['state']. "'";
else
$edits.=", `state` = '" .$_POST['state']. "'";
} else $this->errorid[] = "8";
}}
if(!empty($_POST['pin'])) {
if($this->pin != $_POST['pin']) {
if(strlen($_POST['pin'])<7 && preg_match('/^[0-9]+$/i', $_POST['pin'])) {
if($edits == "")
$edits="`pin` = '" .$_POST['pin']. "'";
else
$edits.=", `pin` = '" .$_POST['pin']. "'";
} else $this->errorid[] = "9";
}}
if(!empty($_POST['country'])) {
if($this->country != $_POST['country']) {
if(strlen($_POST['country'])>4 && strlen($_POST['country'])<10 && preg_match('/^[a-z]+$/i', $_POST['country'])) {
if($edits == "")
$edits="`country` = '" .$_POST['country']. "'";
else
$edits.=", `country` = '" .$_POST['country']. "'";
} else $this->errorid[] = "10";
}}
if(!empty($_POST['phone'])) {
if($this->phone != $_POST['phone']) {
$_POST['phone']=substr($_POST['phone'],3);
if(strlen($_POST['phone'])==10 && preg_match('/^[0-9]+$/i', $_POST['phone'])) {
$_POST['phone']="+91" . $_POST['phone'];
if($edits == "")
$edits="`phone` = '" .$_POST['phone']. "'";
else
$edits.=", `phone` = '" .$_POST['phone']. "'";
} else $this->errorid[] = "4";
}}
if(!empty($_POST['email'])) {
if($this->email != $_POST['email']) {
if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
if($edits == "")
$edits="`email` = '" .$_POST['email']. "'";
else
$edits.=", `email` = '" .$_POST['email']. "'";
} else $this->errorid[] = "3";
}}
if($edits != "") {
$sql="UPDATE `db_user`.`prepayment` SET " .$edits. " WHERE `prepayment`.`userid` = " .$this->userid. " limit 1;";
$con=new dbconnect("db_user");
$con->q_run($sql);
unset($con);
}}
}

class personal {
public $userid;
public $username;
public $name;
public $bday;
public $email;
public $phone;
public $sex;
public $college;
public $errorid=array();

public function extractdata() {
$con=new dbconnect("db_user");
$con->q_exe("select * from personal where userid = :userid limit 1;", array("userid"=>$this->userid));
$rows=$con->rows;
unset($con);
$this->username=$rows[0]['username'];
$this->name=$rows[0]['name'];
$this->bday=$rows[0]['bday'];
$this->email=$rows[0]['email'];
$this->phone=$rows[0]['phone'];
$this->sex=$rows[0]['sex'];
$this->college=$rows[0]['college'];
}

public function editdata() {
$this->extractdata();
$edits="";
if(!empty($_POST['name'])) {
if($this->name != $_POST['name']) {
if(strlen($_POST['name'])>4 && strlen($_POST['name'])<30 && preg_match('/^[a-z ]+[ ]+[a-z]+$/i', $_POST['name']))
$edits="`name` = '" .$_POST['name']. "'";
else $this->errorid[] = "1";
}}
if(!empty($_POST['bday'])) {
$obj = DateTime::createFromFormat('d M y', $_POST['bday']);
if($obj == false)
$this->errorid[] = "2";
else {
if($this->bday != $_POST['bday']) {
if($edits == "")
$edits="`bday` = '" .$_POST['bday']. "'";
else
$edits.=", `bday` = '" .$_POST['bday']. "'";
}}}
if(!empty($_POST['email'])) {
if($this->email != $_POST['email']) {
if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
if($edits == "")
$edits="`email` = '" .$_POST['email']. "'";
else
$edits.=", `email` = '" .$_POST['email']. "'";
} else $this->errorid[] = "3";
}}
if(!empty($_POST['phone'])) {
if($this->phone != $_POST['phone']) {
$_POST['phone']=substr($_POST['phone'],3);
if(strlen($_POST['phone'])==10 && preg_match('/^[0-9]+$/i', $_POST['phone'])) {
$_POST['phone']="+91" . $_POST['phone'];
if($edits == "")
$edits="`phone` = '" .$_POST['phone']. "'";
else
$edits.=", `phone` = '" .$_POST['phone']. "'";
} else $this->errorid[] = "4";
}}
if(!empty($_POST['sex'])) {
if($this->sex != $_POST['sex']) {
if($_POST['sex'] == 'M' || $_POST['sex'] == 'F') {
if($edits == "")
$edits="`sex` = '" .$_POST['sex']. "'";
else
$edits.=", `sex` = '" .$_POST['sex']. "'";
} else $this->errorid = "Sex";
}}
if(!empty($_POST['college'])) {
if($this->college != $_POST['college']) {
if(strlen($_POST['college'])>1 && strlen($_POST['college'])<50 && preg_match('/^[a-z ]+$/i', $_POST['college'])) {
if($edits == "")
$edits="`college` = '" .$_POST['college']. "'";
else
$edits.=", `college` = '" .$_POST['college']. "'";
} else $this->errorid[] = "5";
}}
if($edits != "") {
$sql="UPDATE `db_user`.`personal` SET " .$edits. " WHERE `personal`.`userid` = " .$this->userid. " limit 1;";
$con=new dbconnect("db_user");
$con->q_run($sql);
unset($con);
}}
}
class restart_task {
private $userid;
private $pid;
public $errorid=0;

function __construct($userid,$pid) {
$this->userid=$userid;
$this->pid=$pid;
$this->check_pid();
}

private function check_pid() {
$con=new dbconnect("db_counter");
$con->q_exe("select userid, stage, time from print_task where pid = :pid limit 1;", array("pid"=>$this->pid));
$rows=$con->rows;
unset($con);
if(count($rows) == 1 && $rows[0]['userid'] == $this->userid && in_array($rows[0]['stage'],array("1","2")) && ($rows[0]['time']+600) < time())
$this->errorid=0;
else
$this->errorid=1;
}

public function allow_restart() {
$con=new dbconnect("db_counter");
$con->q_exe("UPDATE `db_counter`.`print_task` SET `stage` = '0', `time` = :time WHERE `print_task`.`pid` = :pid limit 3;", array("pid"=>$this->pid,"time"=>time()));
$con->q_exe("select fileid from print_task where pid = :pid limit 3;",array("pid"=>$this->pid));
$rows=$con->rows;
unset($con);
$con=new dbconnect("db_printhub");
foreach($rows as $fileid) {
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `linked` = 1 WHERE `pdf_files`.`fileid` = :fileid limit 1;", array("fileid"=>$fileid['fileid']));
}
unset($con);
$this->errorid=5;
}
}

class reprint_task {
private $userid;
private $pid;
public $errorid=0;

function __construct($userid,$pid) {
$this->userid=$userid;
$this->pid=$pid;
$this->check_pid();
}

private function check_pid() {
$con=new dbconnect("db_counter");
$con->q_exe("select userid, stage, time from print_task where pid = :pid limit 1;", array("pid"=>$this->pid));
$rows=$con->rows;
unset($con);
if(count($rows) == 1 && $rows[0]['userid'] == $this->userid && $rows[0]['stage'] == 3 && ($rows[0]['time']+172800) > time())
$this->errorid=0;
else
$this->errorid=1;
}

public function reprint() {
$reason=substr($_POST['reason'],0,254);
$con=new dbconnect("db_counter");
$con->q_exe("select sno from reprint_task where reprinted = 0 and pid = :pid limit 1;",array("pid"=>$this->pid));
if(count($con->rows) == 1) {
$this->errorid = 3;
return false;
}
$con->q_exe("INSERT INTO `db_counter`.`reprint_task` (`sno`, `pid`, `userid`, `reason`, `reprinted`, `time`) VALUES ('0', :pid, :userid, :reason, 0, :time);", array("pid"=>$this->pid,"userid"=>$this->userid,"reason"=>$reason,"time"=>time()));
unset($con);
$this->errorid=2;
}

public function allow_reprint() {
$con=new dbconnect("db_counter");
$con->q_exe("UPDATE `db_counter`.`print_task` SET `reprint` = '1', `stage` = '0' WHERE `print_task`.`pid` = :pid limit 3;", array("pid"=>$this->pid));
$con->q_exe("select fileid from print_task where pid = :pid limit 3;",array("pid"=>$this->pid));
$rows=$con->rows;
unset($con);
$con=new dbconnect("db_printhub");
foreach($rows as $fileid) {
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `linked` = 1 WHERE `pdf_files`.`fileid` = :fileid limit 1;", array("fileid"=>$fileid['fileid']));
}
unset($con);
}
}

class reset_p {
private $phone;
private $code;
private $password;
private $userid;
public $errorid=0;
public $attempt;

public function block_account() {
if($this->attempt>5) {
$con=new dbconnect("db_credentials");
$con->q_exe("UPDATE `db_credentials`.`account` SET `blocked` = :time WHERE `account`.`userid` = :userid limit 1;", array("userid"=>$this->userid,'time'=>time()+3600));
unset($con);
$this->reset();
return 4;
}
return 0;
}

private function check_phone($phone) {
$con=new dbconnect("db_credentials");
$con->q_exe("select userid from account where (blocked < :time) and (phone = :phone or altphone = :phone) limit 1;", array('phone'=>$phone, 'time'=>time()));
$rows=$con->rows;
unset($con);
if(count($rows)==1) {
$this->userid = $rows[0]['userid'];
return 0;}
return 4;
}

public function cookie($name,$value,$do='set') {
if($do == 'set')
$time=0;
else
$time=time()-43200;
if($name == "rerr")
$time=time()+3;
setcookie($name,$value,$time,'/','.orntel.com',false,true);
}

private function count_attempt() {
if(empty($_COOKIE['_rat'])) {
$this->cookie('_rat','1');
$this->attempt=1;
} else {
$this->cookie('_rat',$_COOKIE['_rat']+1);
$this->attempt=$_COOKIE['_rat']+1;
}}

public function reset() {
$this->cookie('_rph','0','unset');
$this->cookie('_rco','0','unset');
$this->cookie('_rst','0','unset');
$this->cookie('_rat','0','unset');
$this->cookie('rerr','0','unset');
}

public function reset_password() {
$con=new dbconnect("db_credentials");
$con->q_exe("UPDATE `db_credentials`.`account` SET `password` = :password WHERE `account`.`userid` = :userid limit 1;", array("userid"=>$this->userid,'password'=>$this->password));
unset($con);
$this->reset();
}

private function send_code() {
$code=mt_rand(1111,9999);
$this->code=substr(md5(sha1($code)),0,10);
$con=new dbconnect("db_counter");
$con->q_exe("select time from msgcounter where phone = :phone order by time desc limit 3;", array('phone'=>$this->phone));
$rows=$con->rows;
unset($con);
if(count($rows)==3 && (time() - $rows[2]['time']) < 300)
return 3;
else {
send_msg($this->phone,1,$code); return 0;}
}

public function set_code() {
if(!empty($_POST['verify']) && strlen($_POST['verify'])==4 && preg_match('/^[0-9]+$/i', $_POST['verify'])) {
$this->code=$_POST['verify'];
$this->count_attempt();
$this->errorid=$this->block_account();
if($this->errorid == 0) {
$this->errorid=$this->verify_code('post');
if($this->errorid == 0)
$this->cookie("_rco",$this->code);
}}
elseif(!empty($this->password) && !empty($_COOKIE['_rco']) && strlen($_COOKIE['_rco'])==4 && preg_match('/^[0-9]+$/i', $_COOKIE['_rco'])) {
$this->code=$_COOKIE['_rco'];
$this->errorid=$this->verify_code('cookie');
} else $this->errorid=2;
}

public function set_password() {
if(!empty($_POST['password']) && strlen($_POST['password'])>6) {
if(!empty($_POST['cpassword']) && $_POST['password']==$_POST['cpassword']) {
$this->password=substr(md5(sha1(strtolower($_POST['password']))),0,10);
} else $this->errorid=6;
} else $this->errorid=5;
}

public function set_phone() {
if(!empty($_POST['phone']) && strlen($_POST['phone'])==10 && preg_match('/^[0-9]+$/i', $_POST['phone'])) {
$this->errorid=$this->check_phone('+91' . $_POST['phone']);
if($this->errorid == 0) {
$this->phone="+91" . $_POST['phone'];
$this->errorid=$this->send_code();
if($this->errorid == 0)
$this->cookie("_rph",$this->phone);
}} elseif(!empty($_COOKIE['_rph']) && strlen($_COOKIE['_rph'])==13 && preg_match('/^[0-9+]+$/i', $_COOKIE['_rph'])) {
$this->phone=$_COOKIE['_rph'];
$this->check_phone($this->phone);
}
else $this->errorid=1;
}

private function verify_code($by) {
$con = new dbconnect("db_counter");
$con->q_exe("select value from msgcounter where phone = :phone order by time desc limit 1;", array('phone'=>$this->phone));
$row = $con->rows[0]['value'];
unset($con);
if($row == substr(md5(sha1($this->code)),0,10))
return 0;
else {
if($by == 'cookie')
return 7;
return 2;
}}
}

class reset_u {
public $phone;
public $answer;
private $userid;
private $username;

public function recover_u() {
$con=new dbconnect("db_credentials");
$con->q_exe("select userid from account where answer = :answer and (phone = :phone or altphone = :phone) limit 1;",array("answer"=>$this->answer,"phone"=>$this->phone));
$rows=$con->rows;
unset($con);
if(count($rows) == 1) {
$this->userid=$rows[0]['userid'];
$con=new dbconnect("db_user");
$con->q_exe("select username from personal where userid = :userid limit 1;",array("userid"=>$this->userid));
$this->username=$con->rows[0]['username'];
unset($con);
send_msg($this->phone,6,$this->username);
return true;
}
return false;
}
}

class session {
private $username;
private $password;
public $userid;
private $sessionid;
public $errorid;
private $attempt;
private $maxlogin;
private $maxduration;

private function account_info() {
$con=new dbconnect("db_credentials");
$con->q_exe("select maxlogin, maxduration, active, blocked from account where userid = :userid limit 1;", array("userid"=>$this->userid));
$rows=$con->rows;
unset($con);
$this->maxlogin=$rows[0]['maxlogin'];
$this->maxduration=$rows[0]['maxduration'];
$this->active=$rows[0]['active'];
$this->blocked=$rows[0]['blocked'];
}

private function check_credentials() {
$con=new dbconnect("db_credentials");
$con->q_exe("select userid from account where username = :username and password = :password limit 1;", array("username"=>$this->username,"password"=>$this->password));
$rows=$con->rows;
if(count($rows) == 0) {
unset($con);
return 1;}
$this->userid=$rows[0]['userid'];
if(!empty($_COOKIE['error']) && $_COOKIE['error'] == 4)
$con->q_exe("UPDATE `db_credentials`.`account` SET `active` = 1 WHERE `account`.`userid` = :userid limit 1;", array("userid"=>$this->userid));
elseif(!empty($_COOKIE['error']) && $_COOKIE['error'] == 5)
$con->q_exe("UPDATE `db_credentials`.`session` SET `logout` = :time WHERE (`session`.`userid` = :userid and `session`.`logout` = 0) limit 1;", array("time"=>time(),"userid"=>$this->userid));
unset($con);
$this->account_info();
if($this->blocked>time()) {
$this->cookie("_da",$this->blocked-time());
return 3;
} elseif($this->active == 0) {
return 4; 
} elseif($this->maxlogin == $this->get_active_session()) {
$this->cookie("_da",$this->maxlogin);
return 5;
} else return 0;
}

public function check_sessionid() {
if(empty($_COOKIE['_id']))
return false;
$this->sessionid=$_COOKIE['_id'];
$con=new dbconnect("db_credentials");
$con->q_exe("select userid from session where (sessionid = :sessionid and logout = 0) limit 1;",array("sessionid"=>$this->sessionid));
$rows=$con->rows;
unset($con);
if(count($rows)==0) {
$this->cookie("_id","0","unset");
return false;
}
$this->userid=$rows[0]['userid'];
$this->account_info();
$this->get_active_session();
$con=new dbconnect("db_credentials");
$con->q_exe("select userid from session where (sessionid = :sessionid and logout = 0) limit 1;",array("sessionid"=>$this->sessionid));
$rows=$con->rows;
unset($con);
if(count($rows)==0) {
$this->cookie("_id","0","unset");
return false;
}
return true;
}

public function cookie($name,$value,$do='set') {
if($do == 'set')
$time=0;
else
$time=time()-43200;
setcookie($name,$value,$time,'/','.orntel.com',false,true);
}

private function create_sessionid() {
$this->sessionid=md5(sha1(sha1(time() . mt_rand(10,100))) . time());
}

private function get_active_session() {
$time=time()-$this->maxduration;
$con=new dbconnect("db_credentials");
$sql="UPDATE `db_credentials`.`session` SET `logout` = " .time(). " WHERE (`session`.`userid` = " .$this->userid. " and `session`.`logout` = 0 and `session`.`login` < " .$time. ") limit " .$this->maxlogin. ";";
$con->q_run($sql);
$sql="select userid from session where userid = " .$this->userid. " and logout = 0 limit " .$this->maxlogin. ";";
$con->q_exe($sql,array());
$rows=$con->rows;
unset($con);
return count($rows);
}

public function logout() {
if(!$this->check_sessionid())
return true;
$con=new dbconnect("db_credentials");
$con->q_exe("UPDATE `db_credentials`.`session` SET `logout` = :time WHERE (`session`.`sessionid` = :sessionid and `session`.`logout` = 0) limit 1;",array("time"=>time(),"sessionid"=>$this->sessionid));
unset($con);
$this->cookie("_id","0","unset");
return true;
}

private function set_details() {
if(!empty($_POST['u'])) {
$this->username=substr(md5(sha1($_POST['u'])),0,10);
$this->errorid=0;
} else $this->errorid=1;
if(!empty($_POST['p'])) {
$this->password=substr(md5(sha1(strtolower($_POST['p']))),0,10);
$this->errorid=0;
} else $this->errorid=1;
}

public function set_sessionid() {
$this->set_details();
if($this->errorid == 0) {
$this->errorid=$this->check_credentials();
if($this->errorid == 0) {
$this->create_sessionid();
$con=new dbconnect("db_credentials");
$con->q_exe("INSERT INTO `db_credentials`.`session` (`sessionid`, `userid`, `login`, `logout`, `ipadd`) VALUES (:sessionid, :userid, :time, '0', :ipadd);", array("sessionid"=>$this->sessionid,"userid"=>$this->userid,"time"=>time(),"ipadd"=>$_SERVER['REMOTE_ADDR']));
unset($con);
$this->cookie('error','0','unset');
$this->cookie('_da','0','unset');
$this->cookie('_id',$this->sessionid);
//$GLOBALS['idse']=$this->sessionid;
}}}
}

class predefined {
public $fileid;
public $userid;
private $pages;
private $filename;
private $description;
private $uploader;
private $tag=array();
public $errorid=0;

public function add_predefined() {
if(!$this->check_predefined())
return false;
$value="";
foreach($this->tag as $tag) {
if($value == "")
$value="('" .$this->fileid. "', '" .$tag. "')";
else
$value.=", ('" .$this->fileid. "', '" .$tag. "')";
}
unset($this->tag);
$value="INSERT INTO `db_printhub`.`tags_container` (`fileid`, `tid`) VALUES " .$value. ";";
$con=new dbconnect("db_printhub");
$con->q_exe("DELETE FROM `db_printhub`.`tags_container` WHERE `tags_container`.`fileid` = :fileid;",array("fileid"=>$this->fileid));
$con->q_run($value);
unset($value);
$con->q_exe("DELETE FROM `db_printhub`.`predefined` WHERE `predefined`.`fileid` = :fileid limit 1;",array("fileid"=>$this->fileid));
$con->q_exe("INSERT INTO `db_printhub`.`predefined` (`fileid`, `pages`, `name`, `description`, `uploader`, `time`) VALUES (:fileid, :pages, :name, :desc, :uploader, :time);",array("fileid"=>$this->fileid,"pages"=>$this->pages,"name"=>$this->filename,"desc"=>$this->description,"uploader"=>$this->uploader,"time"=>time()));
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `predefined` = 1, `linked` = 1 WHERE `pdf_files`.`fileid` = :fileid limit 1;", array("fileid"=>$this->fileid));
unset($con);
return true;
}

private function check() {
if($this->errorid == 0)
return true;
else {
$this->cookie("uerr",$this->errorid);
return false;
}
}

public function check_predefined() {
$this->set_pages();
if(!$this->check())
return false;
$this->set_filename();
if(!$this->check())
return false;
$this->set_description();
if(!$this->check())
return false;
$this->set_tags();
if(!$this->check())
return false;
$this->set_uploader();
return true;
}

public function cookie($name,$value,$do='set') {
if($do == 'set')
$time=time()+3;
else
$time=time()-43200;
setcookie($name,$value,$time,'/','.orntel.com',false,true);
}

public function delete_predefined() {
$con=new dbconnect("db_printhub");
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `predefined` = 0, `linked` = 0 WHERE `pdf_files`.`fileid` = :fileid limit 1;",array("fileid"=>$this->fileid));
$con->q_exe("DELETE FROM `db_printhub`.`predefined` WHERE `predefined`.`fileid` = :fileid limit 1;",array("fileid"=>$this->fileid));
$con->q_exe("DELETE FROM `db_printhub`.`tags_container` WHERE `tags_container`.`fileid` = :fileid;",array("fileid"=>$this->fileid));
unset($con);
}

private function set_description() {
if(!empty($_POST['filedes']) && strlen($_POST['filedes'])<50 && preg_match('/^[a-z0-9 ._+-]+$/i', $_POST['filedes']))
$this->description=$_POST['filedes'];
else $this->errorid=11;
if(!empty($_POST['filedes']))
$this->cookie("_fd",$_POST['filedes']);
}

private function set_filename() {
if(!empty($_POST['filename']) && strlen($_POST['filename'])<20 && preg_match('/^[a-z0-9 ._+-]+$/i', $_POST['filename']))
$this->filename=$_POST['filename'];
else $this->errorid=10;
if(!empty($_POST['filename']))
$this->cookie("_fn",$_POST['filename']);
}

private function set_pages() {
if(!empty($_POST['pages']) && preg_match('/^[0-9]+$/i', $_POST['pages']))
$this->pages=$_POST['pages'];
else $this->errorid=9;
if(!empty($_POST['pages']))
$this->cookie("_fp",$_POST['pages']);
}

private function set_tags() {
if(!empty($_POST['category']) && $_POST['category'] == "other") {
$this->tag[]=1;
} elseif(!empty($_POST['category']) && $_POST['category'] == "study") {
if(!empty($_POST['tag'])) {
$con=new dbconnect("db_printhub");
$con->q_exe("select tid from tags order by tid desc limit 1;", array());
$tid=$con->rows[0]['tid']+1;
unset($con);
foreach($_POST['tag'] as $tag) {
if($tag>0 && $tag<$tid)
$this->tag[]=$tag;
}
if(empty($this->tag))
$this->errorid=12;
else {
$this->tag=array_unique($this->tag);
}
} else $this->errorid=12; }
}

private function set_uploader() {
$con=new dbconnect("db_user");
$con->q_exe("select name from personal where userid = :userid limit 1;", array('userid'=>$this->userid));
$this->uploader=$con->rows[0]['name'];
unset($con);
}
}

class upload {
private $upload_dir="../files/";
private $filehash;
public $errorid;
public $fileid;
private $userid;
private $mode;

function __construct($mode,$userid) {
$this->mode=$mode;
$this->userid=$userid;
}

private function add_file() {
$this->fileid=md5($this->filehash . time());
$con=new dbconnect("db_printhub");
$con->q_exe("INSERT INTO `db_printhub`.`pdf_files` (`fileid`, `filename`, `userid`, `time`, `predefined`, `linked`, `verified`) VALUES (:fileid, :filename, :userid, :time, 0, 0, 0);", array("fileid"=>$this->fileid,"filename"=>$this->filehash,"userid"=>$this->userid,"time"=>time()));
unset($con);
}

private function file_exists() {
$con=new dbconnect("db_printhub");
$con->q_exe("select fileid, predefined from pdf_files where filename = :filehash limit 1;",array("filehash"=>$this->filehash));
$rows = $con->rows;
unset($con);
if(count($rows) == 0)
return 0;
$this->fileid=$rows[0]['fileid'];
if($rows[0]['predefined'] == 1)
return 1;
else
return 2;
}

private function predefined() {
$var=$this->file_exists();
if($var == 2) {
$con=new dbconnect("db_printhub");
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `userid` = :userid, `time` = :time, `predefined` = 0, `linked` = 0 WHERE `pdf_files`.`fileid` = :fileid limit 1;",array("userid"=>$this->userid,"time"=>time(),"fileid"=>$this->fileid));
unset($con);
return 0;
} elseif($var == 1)
return 1;
else return 2;
}

private function replacefile() {
$con=new dbconnect("db_printhub");
$con->q_exe("select filename from pdf_files where fileid = :fileid and userid = :userid and predefined = 1 and linked = 1 limit 1;",array("fileid"=>$this->fileid,"userid"=>$this->userid));
if(count($con->rows) == 0) {
unset($con);
return false;
}
if(unlink($this->upload_dir . $con->rows[0]['filename'] . ".pdf")) {
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `filename` = :filename, `verified` = 0 WHERE `pdf_files`.`fileid` = :fileid limit 1;", array("filename"=>$this->filehash,"fileid"=>$this->fileid));
unset($con);
return true;
} else {
unset($con);
return false;}
}

private function undefined() {
$var=$this->file_exists();
if($var == 2) {
$con=new dbconnect("db_printhub");
$con->q_exe("UPDATE `db_printhub`.`pdf_files` SET `linked` = 1 WHERE `pdf_files`.`fileid` = :fileid limit 1;",array("fileid"=>$this->fileid));
unset($con);
return true;
} elseif($var == 1) return true;
else return false;
}

public function uploadfile($key,$error) {
if($error == UPLOAD_ERR_OK) {
if($_FILES['filecontainer']['size'][$key] < 50000000) {
if(pathinfo($_FILES['filecontainer']['name'][$key], PATHINFO_EXTENSION) == "pdf") {
if($_FILES['filecontainer']['type'][$key] == "application/pdf") {
if(file_get_contents($_FILES['filecontainer']['tmp_name'][$key], false, null, 0, 4) == "%PDF") {
if(is_uploaded_file($_FILES['filecontainer']['tmp_name'][$key])) {
$this->filehash=md5_file($_FILES['filecontainer']['tmp_name'][$key]);
if($this->mode == "predefined") {
$var = $this->predefined();
if($var == 0)
$this->errorid=0;
elseif($var == 1)
$this->errorid=7;
else {
move_uploaded_file($_FILES['filecontainer']['tmp_name'][$key], $this->upload_dir . $this->filehash . ".pdf");
$this->add_file();
$this->errorid=0;
}
} elseif($this->mode == "replace") {
if($this->replacefile()) {
move_uploaded_file($_FILES['filecontainer']['tmp_name'][$key], $this->upload_dir . $this->filehash . ".pdf");
$this->errorid=0;
} else $this->errorid=8;
} else {
if(!$this->undefined()) {
move_uploaded_file($_FILES['filecontainer']['tmp_name'][$key], $this->upload_dir . $this->filehash . ".pdf");
$this->add_file();
$this->errorid=0;
} else $this->errorid=0;
}
} else $this->errorid=6;
} else $this->errorid=5;
} else $this->errorid=4;
} else $this->errorid=3;
} else $this->errorid=2;
} else $this->errorid=1; }}

class printtask {
public $fileid;
public $userid;
private $printer;
private $noc;
private $ptype;
private $mode;
public $status;
public $errorid=0;

public function add_task($file) {
$this->set_copies();
if(!$this->check())
return false;
$this->set_mode();
if(!$this->check())
return false;
$this->set_printer();
if(!$this->check())
return false;
$this->set_ptype();
if(!$this->check())
return false;
$file1=fopen("printid.txt","r");
$pid=intval(fread($file1,10));
fclose($file1);
$pid=$pid + mt_rand(1,9);
$time=time();
$val="";
foreach($file as $fileid) {
if($val=="")
$val="(0, '" .$pid. "', '" .$fileid. "', " .$this->userid. ", " .$this->printer. ", " .$this->noc. ", 0, " .$this->ptype. ", " .$this->mode. ", 0, 0, 0, " .$time. ")";
else
$val.=", (0, '" .$pid. "', '" .$fileid. "', " .$this->userid. ", " .$this->printer. ", " .$this->noc. ", 0, " .$this->ptype. ", " .$this->mode. ", 0, 0, 0, " .$time. ")";
}
$val="INSERT INTO `db_counter`.`print_task` (`sno`, `pid`, `fileid`, `userid`, `prnid`, `copies`, `pages`, `bw`, `free`, `interface`, `stage`, `reprint`, `time`) VALUES " .$val. ";";
$con=new dbconnect("db_counter");
$con->q_exe("select time from print_task where userid = :userid order by time desc limit 1;",array("userid"=>$this->userid));
if(count($con->rows) == 1 && (time() - $con->rows[0]['time'] < 60)) {
unset($con);
setcookie("perr","14",time()+3,'/','.orntel.com',false,true);
return false;
} else {
$con->q_run($val);
unset($con);
$file2=fopen("printid.txt","w");
fwrite($file2,$pid);
fclose($file2);
alertprinter($this->printer);
return true;
}}

private function check() {
if($this->errorid != 0) {
setcookie("perr",$this->errorid,time()+3,'/','.orntel.com',false,true);
return false;
}
return true;
}

public function check_fileid() {
if(strlen($this->fileid) != 32)
return false;
$con=new dbconnect("db_printhub");
$con->q_exe("select fileid from pdf_files where fileid = :fileid limit 1;", array('fileid'=>$this->fileid));
$rows=count($con->rows);
unset($con);
if($rows == 0)
return false;
else
return true;
}

private function set_copies() {
if(!empty($_POST['noc']) && preg_match('/^[0-9]+$/i', $_POST['noc'])) {
if($_POST['noc']>0)
$this->noc=$_POST['noc'];
else $this->errorid=12;
} else $this->errorid=12;
}

private function set_mode() {
if(!empty($_POST['mode'])) {
if($_POST['mode'] == "free")
$this->mode=1;
elseif($_POST['mode'] == "paid")
$this->mode=0;
else $this->errorid=11;
} else $this->errorid=11;
}

private function set_printer() {
if(!empty($_POST['printer']) && preg_match('/^[0-9]+$/i', $_POST['printer'])) {
$con=new dbconnect("db_printhub");
$con->q_exe("select pid from printers where pid = :pid limit 1;", array('pid'=>$_POST['printer']));
$rows=count($con->rows);
unset($con);
if($rows == 1)
$this->printer=$_POST['printer'];
else $this->errorid=13; } else $this->errorid=13;
}

private function set_ptype() {
if(!empty($_POST['ptype'])) {
if($_POST['ptype'] == "bw")
$this->ptype=1;
elseif($_POST['ptype'] == "cl")
$this->ptype=0;
else $this->errorid=10;
} else $this->errorid=10; }
}

?>