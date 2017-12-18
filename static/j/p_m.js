function reprintfile(e) {
if(e.elements['reason'].style.display == "none") {
e.elements['reason'].style.display = "";
return false;
}
e.submit();
}
function restartfile(e) {
var r = confirm("Are you sure you want to Restart this Print task?");
if(r)
e.submit();
}
function deletefile(e) {
var r = confirm("Are you sure you want to delete this file?");
if(r)
e.submit();
}
function d(i) {
if(document.getElementById('mail'+i).style.display == "none")
document.getElementById('mail'+i).style.display = "";
else
document.getElementById('mail'+i).style.display = "none";
}
function deletemail(e) {
var r = confirm("Are you sure you want to delete this mail?");
if(r)
e.submit();
}