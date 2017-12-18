<?php
include 'dbconnect.php';
include 'classes.php';

function searchfiles($c, $limit, $p) {
$t="";
$t1="";
switch($c) {
case 1:
$t="select distinct fileid from tags_container limit " .$limit. ", 6;";
$t1="select count(distinct(fileid)) as tfile from tags_container;";
break;
case 2:
$arr=$GLOBALS['arr_'];
$con=new dbconnect("db_printhub");
foreach($arr as $val) {
if($val == "1" || !is_numeric($val))
continue;
$con->q_exe("select category from tags where tid = :tid limit 1;",array("tid"=>$val));
if(count($con->rows) == 0)
continue;
switch($con->rows[0]['category']) {
case "bra":
if($t == "")
$t=strval($val);
else
$t.=", " . strval($val);
break;
case "sem":
if($t1 == "")
$t1=strval($val);
else
$t1.=", " . strval($val);
break;
}
}
unset($con);
if($t == "" && $t1 == "") {
$t1="select count(distinct (fileid)) as tfile from tags_container where tid != 1;";
$t="select distinct fileid from tags_container where tid != 1 limit " .$limit. ", 6;";
} elseif ($t != "" && $t1 == "") {
$t1="select count(distinct (fileid)) as tfile from tags_container where tid in (" .$t. ");";
$t="select distinct fileid from tags_container where tid in (" .$t. ") limit " .$limit. ", 6;";
} elseif ($t != "" && $t1 != "") {
$temp = $t1;
$t1="select count(fileid) as tfile from tags_container where tid in (" .$t. ") and fileid in (select fileid from tags_container where tid in (" .$temp. "));";
$t="select fileid from tags_container where tid in (" .$t. ") and fileid in (select fileid from tags_container where tid in (" .$temp. ")) limit " .$limit. ", 6;";
} elseif ($t == "" && $t1 != "") {
$t="select distinct fileid from tags_container where tid in (" .$t1. ") limit " .$limit. ", 6;";
$t1="select count(distinct (fileid)) as tfile from tags_container where tid in (" .$t1. ");";
}
break;
case 3:
$t1="select count(distinct (fileid)) as tfile from tags_container where tid = 1;";
$t="select distinct fileid from tags_container where tid = 1 limit " .$limit. ", 6;";
}
$total="0";
$data=array();
$data['name']=array();
$data['description']=array();
$data['uploader']=array();
$data['time']=array();
$data['pages']=array();
$data['printed']=array();
$data['fileid']=array();
$data['verified']=array();
$con=new dbconnect("db_printhub");
$con->q_exe($t1,array());
$total=$con->rows[0]['tfile'];
$con->q_exe($t,array());
$rows=$con->rows;
if(count($rows) == 0) {
echo "<div class='ff'>No File found.</div>";
unset($con);
} else {
$t="";
foreach($rows as $row) {
$data['fileid'][]=$row['fileid'];
$con->q_exe("select verified from pdf_files where fileid = :fileid limit 1;",array("fileid"=>$row['fileid']));
$data['verified'][]=$con->rows[0]['verified'];
$con->q_exe("select pages, name, description, uploader, time from predefined where fileid = :fileid limit 1;",array("fileid"=>$row['fileid']));
$data['name'][]=$con->rows[0]['name'];
$data['description'][]=$con->rows[0]['description'];
$data['uploader'][]=$con->rows[0]['uploader'];
$data['time'][]=$con->rows[0]['time'];
$data['pages'][]=$con->rows[0]['pages'];
}
unset($con);
$con=new dbconnect("db_counter");
foreach($data['fileid'] as $key=>$val) {
$con->q_exe("select count(sno) as printed from print_task where fileid = :fileid;", array('fileid'=>$val));
$data['printed'][$key]=$con->rows[0]['printed'];
}
unset($con);
if($p==1)
$var="0 - 6";
elseif($p==2)
$var="7 - 12";
else {
$e=6*$p-5;
$var=$e. " - ";
$e=6*$p;
$var=$var . $e;
}
echo "<div class='ff'>" .$total. " Files found. Showing " . $var . "</div>";
foreach($data['fileid'] as $key=>$val) {
$i=$key+1;
echo "<div class='pc'><div class='sno'>" .$i. "</div>
<div>" .$data['name'][$key]. "</div>
<div class='pd'>" .$data['description'][$key]. "</div>
<div class='pup'>- " .$data['uploader'][$key]. "</div>
<div class='pu'>" .date('d M y',$data['time'][$key]). "</div>";
if($data['verified'][$key] == "1")
echo "<div class='fv'>Verified File</div>";
else
echo "<div class='fn'>Not Verified</div>";
echo "<div class='pg'>" .$data['pages'][$key]. " Page(s)</div>
<div class='pp'>Printed " .$data['printed'][$key]. " time(s)</div>
<a class='pr' href='print.php?file1=" .$val. "'>Print</a>
<div class='ps'><div class='fb-share-button' data-href='http://printhub.orntel.com/predefined_share.php?file1=" .$val. "' data-layout='button'></div></div>
</div>
";
}}
}

function mymails() {
$var= new mailbox($GLOBALS['userid'],1);
$rows=$var->showmail();
unset($var);
$i=0;
foreach($rows as $mail) {
$i=$i+1;
echo "<div class='mail_con'><form style='display: none;' name='f" .$i. "' action='v.php' method='post'>
<input type='hidden' name='mid' value='" .$mail['mid']. "' /></form><div class='mp1' onclick='d(" .$i. ");'>
<div class='mail_n mail'>" .$i. "</div><div class='mail_s mail'>" .$mail['subject']. "</div><div class='m_r mail'>+</div></div>
<div class='mail_c' id='mail" .$i. "' style='display: none'>" .$mail['content']. "</div>
<div class='mp2'><label class='mail'>" .date('d M y',$mail['time']). "</label><label class='mail_d mail' onclick='deletemail(f" .$i. ")'>Delete</label></div></div>";
}
if($i==0)
echo "No new messages.";
}

function countmymails() {
$var= new mailbox($GLOBALS['userid'],1);
$rows=$var->countmail();
unset($var);
if($rows != 0)
echo "<div id='m_count'>" .$rows. "</div>";
}

function mycredits() {
$var= new cashier($GLOBALS['userid']);
$var->getpoints();
$var->getcoins();
echo "<div class='cr_s'>
<div class='cr_sh'>Credits</div>
<div class='cr_sv'>" .$var->coins. "</div>
</div><div class='cr_s'>
<div class='cr_sh'>Free Points</div>
<div class='cr_sv'>" .$var->points. "</div></div>
<div class='ofl'>Free points will be renewed every week. Previous balance will be discarded.</div>";
unset($var);
}

function myhistory() {
$con=new dbconnect("db_counter");
$con->q_exe("select pid, prnid, copies, sum(pages) as pages, bw, free, interface, stage, reprint, time from print_task where userid = :userid group by pid order by time desc limit 5;", array("userid"=>$GLOBALS['userid']));
$rows=$con->rows;
unset($con);
$con=new dbconnect("db_printhub");
$i=1;
foreach($rows as $data) {
echo "<div class='pre_c'><div class='c_num'>" .$i. "</div>
Print Id&nbsp;<label>" .$data['pid']. "</label><br />";
$con->q_exe("select name, map from printers where pid = :pid limit 1;", array("pid"=>$data['prnid']));
echo "Printer&nbsp;<a href='" .$con->rows[0]['map']. "' target='_blank'><label>" .$con->rows[0]['name']. "</label></a><br />
Pages&nbsp;<label>" .$data['copies']. "*" .$data['pages']. "</label><br />Type&nbsp;<label>";
if($data['bw'] == 1)
echo "B&W";
else
echo "Colored";
echo "</label><br />Mode&nbsp;<label>";
if($data['free'] == 1)
echo "Free";
else
echo "Paid";
echo "</label><br />Interface&nbsp;<label>";
if($data['interface'] == 1)
echo "OPM";
else
echo "Web";
echo "</label><br />Status&nbsp;";
switch($data['stage']) {
case 0:
echo "<label style='color: #00f'>Pending";
break;
case 1:
echo "<label style='color: #c6cb00'>Received";
break;
case 2:
echo "<label style='color: #f79600'>Processing";
break;
case 3:
echo "<label style='color: #00b000'>Completed";
break;
case 4:
echo "<label style='color: #f00'>Rejected";
break;
case 5:
echo "<label style='color: #00b000'>Reprinted";
}
echo "</label><br />Commanded on&nbsp;<label>";
echo date('d M y',$data['time']);
echo "</label>";
if(($data['time']+172800) > time() && $data['reprint'] == "0" && $data['stage'] == 3) {
echo "<form name='r" .$i. "' action='v.php' method='POST'>
<input type='hidden' name='pid' value='" .$data['pid']. "' />
<input type='hidden' name='reprint' value='1' />
<input style='display: none; width: 180px; margin: 4px auto' type='text' name='reason' placeholder='Reason for reprint.' />
</form>
<div class='_ud' onclick='reprintfile(r" .$i. ")'>Reprint</div>";
} elseif(($data['time']+600) < time() && in_array($data['stage'],array("1","2"))) {
echo "<form name='r" .$i. "' action='v.php' method='POST'>
<input type='hidden' name='pid' value='" .$data['pid']. "' />
<input type='hidden' name='restart' value='1' />
</form>
<div class='_ud' onclick='restartfile(r" .$i. ")'>Restart</div>";
}
echo "</div>";
$i=$i+1;
}
unset($con);
if($i==1)
echo "No history found.";
}

function mypredefined() {
$data=array();
$data['name']=array();
$data['pages']=array();
$data['printed']=array();
$data['tags']=array();
$data['fileid']=array();
$con=new dbconnect("db_printhub");
$con->q_exe("select fileid from pdf_files where userid = :userid and predefined = 1;", array('userid'=>$GLOBALS['userid']));
$rows=$con->rows;
$i=0;
foreach($rows as $row) {
$data['fileid'][$i]=$row['fileid'];
$con->q_exe("select name, pages from predefined where fileid = :fileid limit 1;", array('fileid'=>$row['fileid']));
$data['name'][$i]=$con->rows[0]['name'];
$data['pages'][$i]=$con->rows[0]['pages'];
$con->q_exe("select tid from tags_container where fileid = :fileid;", array('fileid'=>$row['fileid']));
$tags=$con->rows;
$data['tags'][$i]=array();
foreach($tags as $tag) {
$con->q_exe("select name from tags where tid = :tid limit 1;", array('tid'=>$tag['tid']));
$data['tags'][$i][]=$con->rows[0]['name'];
}
unset($tags);
unset($rows);
$i=$i+1;
}
unset($con);
$con=new dbconnect("db_counter");
foreach($data['fileid'] as $key=>$val) {
$con->q_exe("select count(sno) as printed from print_task where fileid = :fileid;", array('fileid'=>$val));
$data['printed'][$key]=$con->rows[0]['printed'];
}
unset($con);
$i=0;
foreach($data['fileid'] as $key=>$val) {
$i=$key+1;
echo "<div class='pre_c'><div class='c_num'>" .$i. "</div><span>" .$data['name'][$key]. "</span><div class='_spc'>
<label>" .$data['pages'][$key]. " Page(s)</label>&nbsp;<label>Printed " .$data['printed'][$key]. " time(s)</label>
</div><div class='_spc'><div>Tagged In:&nbsp;";
foreach($data['tags'][$key] as $t) {
echo "<label>" .$t. "</label>";
}
echo "<form name='u" .$i. "' action='v.php' method='POST'>
<input type='hidden' name='fileid' value='" .$val. "' />
<input type='hidden' name='process' value='1' />
</form>
<form name='d" .$i. "' action='v.php' method='POST'>
<input type='hidden' name='fileid' value='" .$val. "' />
<input type='hidden' name='process' value='2' />
</form>
</div></div>
<div class='ps'><div class='fb-share-button' data-href='http://printhub.orntel.com/predefined_share.php?file1=" .$val. "' data-layout='button'></div></div>
<div class='_ud' onclick='u" .$i. ".submit()'>Edit</div>
<div class='_ud' onclick='deletefile(d" .$i. ")'>Delete file</div></div>";
}
if($i==0)
echo "No predefined list found. <a href='predefined.php' style='color: #00f'>create list here</a>";
else
echo "<div style='text-align: center; padding: 1em 0'><a href='predefined.php' style='color: #00f'>Add New File</a></div>";
}

function getfilename($id) {
$fileid=$_GET['file' .$id];
if(strlen($fileid) != 32){
echo "Unknown File";
return false;
}
$con=new dbconnect("db_printhub");
$con->q_exe("select name from predefined where fileid = :fileid limit 1;", array("fileid"=>$fileid));
$rows=$con->rows;
unset($con);
if(count($rows) == 0) {
if(empty($_GET['filen' .$id]))
echo "Unknown File";
else
echo strip_tags($_GET['filen' .$id]);
}
else
echo $rows[0]['name'];
}

function showprinters() {
$time=time()-180;
$con=new dbconnect("db_printhub");
$con->q_exe("select pid, status from printers where time < :time;", array("time"=>$time));
foreach($con->rows as $row) {
if($row['status'] != 2) {
$con->q_exe("UPDATE `db_printhub`.`printers` SET `status` = '0' WHERE `printers`.`pid` = :pid;", array("pid"=>$row['pid']));
}}
$con->q_exe("select * from printers;", array());
$GLOBALS['printers']=$con->rows;
unset($con);
}

function findtags() {
$con=new dbconnect("db_printhub");
$con->q_exe("select tid, category, name from tags;", array());
$tags=$con->rows;
$var=array();
if(!empty($_COOKIE['fileid'])) {
$con->q_exe("select tid from tags_container where fileid = :fileid;", array("fileid"=>$_COOKIE['fileid']));
$var=$con->rows;
unset($con);
} else unset($con);
$tin=array();
foreach($var as $arr) {
$tin[]=$arr['tid'];
}
return array($tags,$tin);
}

function predval() {
if(!empty($_COOKIE['fileid'])) {
$con=new dbconnect("db_printhub");
$con->q_exe("select pages, name, description from predefined where fileid = :fileid limit 1;",array("fileid"=>$_COOKIE['fileid']));
$GLOBALS['value']=$con->rows;
unset($con);
} else $GLOBALS['value']=array(); }

function pcatg($pl,$tin) {
$cat=1;
if(in_array(1,$tin))
$cat=2;
switch($pl) {
case 1:
if($cat == 1)
echo " checked";
break;
case 2:
if($cat == 2)
echo " checked";
break;
case 3:
if($cat == 2)
echo " style='display: none;'";
break;
}}

function showtags($mode,$tags,$tin) {
foreach($tags as $tag) {
if($mode == $tag['category']) {
echo "<label class='_label' for='t" .$GLOBALS['i']. "'>
<input id='t" .$GLOBALS['i']. "' type='checkbox' name='tag[]' value='" .$tag['tid']. "'"; 
if(in_array($tag['tid'], $tin)) echo " checked";
echo " />" .$tag['name']. "</label>";}
$GLOBALS['i']=$GLOBALS['i']+1;
}}

function send_msg($phone, $temp, $val=0, $val2=0) {
$con=new dbconnect("db_counter");
$con->q_exe("INSERT INTO `db_counter`.`msgcounter` (`mid`, `phone`, `template`, `value`, `time`) VALUES ('0', :phone, :template, :value, :time);", array('phone'=>$phone, 'template'=>$temp, 'value'=>substr(md5(sha1($val)),0,10), 'time'=>time()));
unset($con);
switch ($temp) {
case 1:
$message="This message has been sent for identity verification. Your Orntel Identity Number is " .$val. " .";
break;
case 2:
$message="Your printing task has been completed. Your Print Id is " .$val. " . Please collect your documents at your earliest convenience.";
break;
case 3:
$message="Your printing task has been rejected. Sign in to your Orntel account for error report.";
break;
case 4:
$message="Please sign in to your Orntel account. You have a new message in your MailBox.";
break;
case 5:
$message="Dear " .$val. " , Thank you for signing up. Welcome to Orntel family.";
break;
case 6:
$message="Hi! your Orntel username is " .$val. " .";
break;
case 7:
$message="Hi! " .$val. " coins has been debited from your Orntel account. Your remaining credits are " .$val2. " . Thank You for being part of Orntel family.";
break;
case 8:
$message="Hi! " .$val. " coins has been credited to your Orntel account. Your total credits are " .$val2. " . Thank You for being part of Orntel family.";
break;
default:
return;
}
$ch = curl_init();
$user="blockray7@gmail.com:flightlesswi";
$senderID="ORNTEL"; 
curl_setopt($ch,CURLOPT_URL,  "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "user=" .$user. "&senderID=" .$senderID. "&receipientno=" .$phone. "&msgtxt=" .$message);
$buffer = curl_exec($ch);
curl_close($ch);
if(empty ($buffer))
return false;
else
return true;
}

function chart($display) {
if($display == "today") {
$t1 = mktime(0, 0, 0, date("n"), date("j"));
$t2 = mktime(23, 59, 0, date("n"), date("j"));
} elseif($display == "month") {
$t1 = mktime(0, 0, 0, 1, 1);
$t2 = mktime(0, 0, 0, 12, 31);
} elseif($display == "day") {
$t1 = mktime(0, 0, 0, date("n"), 1);
$t2 = mktime(23, 59, 0, date("n"), date("t"));
}
$con=new dbconnect("db_counter");
$con->q_exe("select (copies*pages) as tpages, free, interface, time from print_task where stage = 3 and time >= :t1 and time <= :t2;", array("t1"=>$t1,"t2"=>$t2));
$rows=$con->rows;
unset($con);
$data=array();
if($display == "today") {
$data['minTickInterval'] = "3600 * 1000";
$data['pointInterval'] = "3600 * 1000";
$data['utc'] = date("Y, m-1, d");
$d=array();
$d['f']=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
$d['p']=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
$d['w']=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
$d['o']=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
$d['web']=0;
$d['opm']=0;
$d['free']=0;
$d['paid']=0;
foreach($rows as $arr) {
$i=1; 
while($i<25) {
$t=$t1+(3600*$i);
if($arr['time']<=$t) {
if($arr['free'] == 1) {
$d['free']=$d['free']+$arr['tpages'];
$d['f'][$i-1]=$d['f'][$i-1]+$arr['tpages'];
} else {
$d['paid']=$d['paid']+$arr['tpages'];
$d['p'][$i-1]=$d['p'][$i-1]+$arr['tpages'];
}
if($arr['interface'] == 0) {
$d['web']=$d['web']+$arr['tpages'];
$d['w'][$i-1]=$d['w'][$i-1]+$arr['tpages'];
} else {
$d['opm']=$d['opm']+$arr['tpages'];
$d['o'][$i-1]=$d['o'][$i-1]+$arr['tpages'];
}
break; }
$i=$i+1; 
}}
unset($rows);
$a[1]="0";
$a[2]="0";
$a[3]="0";
$a[4]="0";
$a[5] = $d['free'];
$a[6] = $d['paid'];
$a[7] = $d['web'];
$a[8] = $d['opm'];
$i=0;
while($i<24) {
$a[1]=$a[1]. ", " . $d['f'][$i];
unset($d['f'][$i]);
$a[2]=$a[2]. ", " . $d['p'][$i];
unset($d['p'][$i]);
$a[3]=$a[3]. ", " . $d['w'][$i];
unset($d['w'][$i]);
$a[4]=$a[4]. ", " . $d['o'][$i];
unset($d['o'][$i]);
$i=$i+1;
}
unset($d);
} elseif($display == "day") {
$data['minTickInterval'] = "24 * 3600 * 1000";
$data['pointInterval'] = "24 * 3600 * 1000";
$data['utc'] = date("Y, m-1, 1");
$ar=array();
$max=intval(date("t"));
$i=$max;
while($i>0) {
$ar[]=0;
$i=$i-1;
}
$max=$max+1;
$d=array();
$d['f']=$ar;
$d['p']=$ar;
$d['w']=$ar;
$d['o']=$ar;
unset($ar);
$d['web']=0;
$d['opm']=0;
$d['free']=0;
$d['paid']=0;
foreach($rows as $arr) {
$i=1;
while($i<$max) {
$t=$t1+(86400*$i);
if($arr['time']<$t) {
if($arr['free'] == 1) {
$d['free']=$d['free']+$arr['tpages'];
$d['f'][$i-1]=$d['f'][$i-1]+$arr['tpages'];
} else {
$d['paid']=$d['paid']+$arr['tpages'];
$d['p'][$i-1]=$d['p'][$i-1]+$arr['tpages'];
}
if($arr['interface'] == 0) {
$d['web']=$d['web']+$arr['tpages'];
$d['w'][$i-1]=$d['w'][$i-1]+$arr['tpages'];
} else {
$d['opm']=$d['opm']+$arr['tpages'];
$d['o'][$i-1]=$d['o'][$i-1]+$arr['tpages'];
} break; }
$i=$i+1; 
}}
unset($rows);
$a[1]=$d['f'][0];
$a[2]=$d['p'][0];
$a[3]=$d['w'][0];
$a[4]=$d['o'][0];
$a[5] = $d['free'];
$a[6] = $d['paid'];
$a[7] = $d['web'];
$a[8] = $d['opm'];
$i=1;
$max=$max-1;
while($i<$max) {
$a[1]=$a[1]. ", " . $d['f'][$i];
unset($d['f'][$i]);
$a[2]=$a[2]. ", " . $d['p'][$i];
unset($d['p'][$i]);
$a[3]=$a[3]. ", " . $d['w'][$i];
unset($d['w'][$i]);
$a[4]=$a[4]. ", " . $d['o'][$i];
unset($d['o'][$i]);
$i=$i+1;
}
unset($d);
} elseif($display == "month") {
$data['minTickInterval'] = "30 * 24 * 3600 * 1000";
$data['pointInterval'] = "30 * 24 * 3600 * 1000";
//$data['utc'] = date("Y, m-1, 1");
$data['utc'] = "2015, 0, 1";
$d=array();
$d['f']=array(0,0,0,0,0,0,0,0,0,0,0,0);
$d['p']=array(0,0,0,0,0,0,0,0,0,0,0,0);
$d['w']=array(0,0,0,0,0,0,0,0,0,0,0,0);
$d['o']=array(0,0,0,0,0,0,0,0,0,0,0,0);
$d['web']=0;
$d['opm']=0;
$d['free']=0;
$d['paid']=0;
foreach($rows as $arr) {
$i=1; 
while($i<13) {
if($i==12)
$t=$t2+1;
else {
$t=$i+1;
$t=mktime(0, 0, 0, $t, 1);
}
if($arr['time']<$t) {
if($arr['free'] == 1) {
$d['free']=$d['free']+$arr['tpages'];
$d['f'][$i-1]=$d['f'][$i-1]+$arr['tpages'];
} else {
$d['paid']=$d['paid']+$arr['tpages'];
$d['p'][$i-1]=$d['p'][$i-1]+$arr['tpages'];
}
if($arr['interface'] == 0) {
$d['web']=$d['web']+$arr['tpages'];
$d['w'][$i-1]=$d['w'][$i-1]+$arr['tpages'];
} else {
$d['opm']=$d['opm']+$arr['tpages'];
$d['o'][$i-1]=$d['o'][$i-1]+$arr['tpages'];
}
break;
}
$i=$i+1; }}
unset($rows);
$a[1]=$d['f'][0];
$a[2]=$d['p'][0];
$a[3]=$d['w'][0];
$a[4]=$d['o'][0];
$a[5] = $d['free'];
$a[6] = $d['paid'];
$a[7] = $d['web'];
$a[8] = $d['opm'];
$i=1;
while($i<12) {
$a[1]=$a[1]. ", " . $d['f'][$i];
unset($d['f'][$i]);
$a[2]=$a[2]. ", " . $d['p'][$i];
unset($d['p'][$i]);
$a[3]=$a[3]. ", " . $d['w'][$i];
unset($d['w'][$i]);
$a[4]=$a[4]. ", " . $d['o'][$i];
unset($d['o'][$i]);
$i=$i+1;
}
unset($d);
}
$data['free'] = $a[1];
$data['paid'] = $a[2];
$data['web'] = $a[3];
$data['opm'] = $a[4];
$data['tfree'] = $a[5];
$data['tpaid'] = $a[6];
$data['tweb'] = $a[7];
$data['topm'] = $a[8];
unset($a);
$GLOBALS['data']=$data;
}


function alertprinter($prnid) {

}

include 'functionz.php';
?>