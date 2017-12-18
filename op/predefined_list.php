<?php include 'header.php';
if(empty($_GET['c']))
$c=1;
else {
if(in_array(intval($_GET['c']),array(1,2,3)))
$c=intval($_GET['c']);
else
$c=1;
}
if(empty($_GET['p']))
$p=1;
elseif(is_numeric($_GET['p']) && $_GET['p']>0)
$p=$_GET['p'];
else
$p=1;
function sh($m) {
$c=$GLOBALS['c'];
switch($m) {
case 1:
if($c == 1)
echo "gs";
else
echo "gb";
break;
case 2:
if($c == 2)
echo "gs";
else
echo "gb";
break;
case 3:
if($c == 3)
echo "gs";
else
echo "gb";
}}
?>
<div id="fb-root"></div>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<section>
<div id='lh'>Predefined List</div>
<div class='cop'><a href='predefined.php'>Create Your own Predefined</a></div>
<div id='mco'>
<div class='<?php sh(1); ?>'>
<div class='gc'><a href='predefined_list.php?c=1'><div class='g <?php sh(1); ?>'>
Show All
</div></a></div><div class='gc'><a href='predefined_list.php?c=2'><div class='g <?php sh(2); ?>'>
Study Material
</div></a></div><div class='gc'><a href='predefined_list.php?c=3'><div class='g <?php sh(3); ?>'>
Other Material
</div></a></div></div>
<div class='gs' style='padding: 0.5em; border-top: 1px solid #aaa'>
<?php if($c == 2) {
$arr_=array();
if(!empty($_GET['tag']))
$arr_=$_GET['tag'];
?>
<form name='f1' method='get' action='predefined_list.php'>
<input type='hidden' name='c' value='2'>
<div class='tac_'><div class='ta'>Branch</div><div class='ard'></div>
<div class='tac'>
<?php
$arr=findtags();
$i=1;
showtags("bra", $arr[0], $arr_); ?>
</div></div><div class='tac_'>
<div class='ta'>Semester</div><div class='ard'></div>
<div class='tac'>
<?php showtags("sem", $arr[0], $arr_); ?>
</div></div>
</form>
<div class='pr' onclick='f1.submit()'>Search</div>
<hr />
<?php }
$link=array();
$link['ta']="";
if(!empty($_GET['tag'])) {
foreach($_GET['tag'] as $v) {
if(is_numeric($v)) {
if($link['ta']=="")
$link['ta']="&tag%5B%5D=" . $v;
else
$link['ta'].="&tag%5B%5D=" . $v;
}}}
$link['np']=$p+1;
$link['pp']=$p-1;
$link['limit']=($p-1)*6;
searchfiles($c,$link['limit'],$p); ?>
</div></div>
<?php echo "<div class='np'><a href='predefined_list.php?c=" .$c. "&p=" .$link['pp'] . $link['ta']."'>Previous Page</a><a href='predefined_list.php?c=" .$c. "&p=" .$link['np'] . $link['ta']."'>Next Page</a></div>"; ?>
</section>
<?php include 'footer.php';?>