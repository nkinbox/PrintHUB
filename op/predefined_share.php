<?php include 'header.php'; ?>
<style>
#sli {
padding: 5em;
text-align: center;
}
#slin {
padding: 5px;
background: #f7f7f7;
font-size: 155%;
border: 1px solid #aaa;
}
</style>
<section>
<div id='sli'>
<?php
if(!empty($_GET['file1']) && strlen($_GET['file1']) == 32)
echo "<a id='slin' href='print.php?file1=" .$_GET['file1']. "'>Proceed to Printer</a>";
else
echo "<a id='slin' href='print.php'>Proceed to Printer</a>";
?>
</div>
</section>
<?php include 'footer.php'; ?>