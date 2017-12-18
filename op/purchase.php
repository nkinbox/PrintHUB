<?php 
if(empty($_GET['buy']) && !in_array($_GET['buy'], array(1,2,3,4))) {
header('Location: http://printhub.orntel.com/buy.php');
exit;
}
header('Location: http://orntel.com/ccavRequest.php?buy=' . $_GET['buy']);
?>