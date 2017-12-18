function add(id) {
document.getElementById('a'+id).style.display='none';
document.getElementById('fc'+id).style.display='';
id=id+1;
if(id<4)
document.getElementById('a'+id).style.display='';
}
function submitf() {
var r = confirm("Once print command is sent it cannot be cancelled. So please re-check the printer selected and Overview container.");
if(r)
f1.submit();
}