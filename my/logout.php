<?php include 'header.php';?>
<section>
<form name='f1' action='v.php' method='post'><input type='hidden' name='logout' value='0'></form>
<form name='f2' action='v.php' method='post'><input type='hidden' name='logout' value='1'></form>
<div class='c'><p>Are you sure you want to logout?</p>
<div class='b' onclick='f1.submit()'>No</div><div class='b y' onclick='f2.submit()'>Yes</div>
</div>
</section>
<?php include 'footer.php';?>