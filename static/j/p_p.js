function addfile() {
document.getElementById("id1").style.display="none";
document.getElementById("id2").style.display="";
}
function tagshow(id) {
if(document.getElementById(id).value=="other")
document.getElementById("tag_c").style.display="none";
else
document.getElementById("tag_c").style.display="";
}