<?php
include '../library/dbconnect.php';
if(empty($_GET['id']))
$id="0";
else
$id=$_GET['id'];
$con=new dbconnect("db_counter");
$con->q_exe("select copies, sum(pages) as tpages, reprint, time from print_task where pid = :pid limit 3;",array("pid"=>$id));
$rows=$con->rows;
unset($con);
if(empty($rows[0]['copies'])) {
echo "Error";
exit;
}
$tpages=$rows[0]['tpages']*$rows[0]['copies'];
?>
<!DOCTYPE html>
<html>
<head>
<style>
b{font-size:29px}
.u{font-size:20px}
#c{float:right;padding-top:1em}
#p{font-size:30px;float:right;line-height:35px}
#t{float:right;margin-right:20px;line-height:35px;}
#r{float:right}
label{width:200px;text-align:right;display:inline-block}
#h{border:1px solid #aaa;margin:1em;padding:1em;}
#i{text-align:center}
#d{margin:1em 0 1em 0;border-top:1px dashed #777;}
#l{float:right;border:1px solid #777;padding:1em;font-size:30px}
</style>
<title>Orntel Printhub Invoice</title>
</head>
<body>
<div id='c'><i>For queries Whatsapp on</i> +91-8130527753<br/>http://printhub.orntel.com</div>
<b>PRINTHUB</b><br/><div class='u'>making easy easier</div>
<hr/>
<div id='p'><?php echo $id; ?></div>
<div id='t'>PrintID</div>
<hr style='clear:both'/><br/>
<div id='r'>Print Executed @ <?php echo date('D, d M Y H:i:s',time()); ?></div>
Print Command @ <?php echo date('D, d M Y H:i:s',$rows[0]['time']); ?><br/>
<div id='h'>
<label>No of copies </label>: <?php echo $rows[0]['copies']; ?><br/>
<label>Total pages </label>: <?php echo $rows[0]['tpages']; ?><br/>
<label>Reprinted </label>: <?php if($rows[0]['reprint'] == "1") echo "Yes"; else echo "No"; ?>
</div>
<div id='i'>Successfully debited <?php echo $tpages; ?> credits</div>
<hr/>
Thank you for using Orntel Printhub.<br/>
This is a computer generated invoice.<br/>
In case of Reprint request keep this invoice for future reference.<br/>
<div id='d'></div>
To be kept with shopkeeper
<div id='l'><?php echo $tpages; ?></div>
<h1><?php echo $id; ?></h1>
<label>Received by </label>: ________________<br/><br/>
<label>Phone Number </label>: ________________
</body>
</html>