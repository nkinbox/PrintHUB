<?php 
include '../library/functions.php';
$var=new ccavenue(0,0,0);
$var->response();
?>
<html>
<head>
<script>
function newDoc() {
window.location.assign("http://printhub.orntel.com/account.php?show=mail");
}
</script>
</head>
<body onload='newDoc()'>
<h1>Redirecting ...</h2>
</body>
</html>
