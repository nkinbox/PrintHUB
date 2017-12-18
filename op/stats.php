<?php include 'header.php';
if(empty($_GET['show']))
$display="day";
else {
switch($_GET['show']) {
case "today":
$display="today";
break;
case "day":
$display="day";
break;
case "month":
$display="month";
break;
default:
$display="day";
}}
chart($display);
?>
<section>
<div id='con'>
<a href='stats.php?show=today' <?php if($display == 'today') echo "class='sel'";?>>Today</a
><a href='stats.php?show=day' <?php if($display == 'day') echo "class='sel'"; ?>>Day</a
><a href='stats.php?show=month' <?php if($display == 'month') echo "class='sel'"; ?>>Month</a
></div>
<div id='chart'></div
><div id='pie1'></div
><div id='pie2'></div>
<script>
var chart= new Highcharts.Chart({
chart: {
type: 'line',
renderTo: 'chart'
},
title: {
text: 'Orntel Printhub Service Data'
},
subtitle: {
text: 'Printed Pages statistics'
},
xAxis: {
type: 'datetime',
minTickInterval: <?php echo $data['minTickInterval'];?>,
dateTimeLabelFormats: {hour:'%l:%M%P'},
},
yAxis: {
allowDecimals: false,
title: {text: 'Number of pages'}
},
series: [{
pointStart: Date.UTC(<?php echo $data['utc'];?>),
pointInterval:  <?php echo $data['pointInterval'];?>,
name: 'Free Printouts',
data: [<?php echo $data['free'];?>]
}, {
pointStart: Date.UTC(<?php echo $data['utc'];?>),
pointInterval:  <?php echo $data['pointInterval'];?>,
name: 'Paid Printouts',
data: [<?php echo $data['paid'];?>]
}, {
visible: false,
pointStart: Date.UTC(<?php echo $data['utc'];?>),
pointInterval:  <?php echo $data['pointInterval'];?>,
name: 'Using Website',
data: [<?php echo $data['web'];?>]
}, {
visible: false,
pointStart: Date.UTC(<?php echo $data['utc'];?>),
pointInterval:  <?php echo $data['pointInterval'];?>,
name: 'Using OPM',
data: [<?php echo $data['opm'];?>]
}]
});
var pie1= new Highcharts.Chart({
chart: {
renderTo: 'pie1',
plotBackgroundColor: null,
plotBorderWidth: null,
plotShadow: false
},
title: {
text: 'Free Vs Paid'
},
tooltip: {
pointFormat: '{series.name}: <b>{point.y}</b><br>Total Pages: {point.total}'
},
plotOptions: {
pie: {
allowPointSelect: true,
cursor: 'pointer',
dataLabels: {
enabled: false
},
showInLegend: true
}
},
series: [{
type: 'pie',
name: 'Pages',
data: [['Free Printouts', <?php echo $data['tfree'];?>],['Paid Printouts', <?php echo $data['tpaid'];?>]]}]
});

var pie2= new Highcharts.Chart({
chart: {
renderTo: 'pie2',
plotBackgroundColor: null,
plotBorderWidth: null,
plotShadow: false
},
title: {
text: 'Web Vs OPM'
},
tooltip: {
pointFormat: '{series.name}: <b>{point.y}</b><br>Total Pages: {point.total}'
},
plotOptions: {
pie: {
allowPointSelect: true,
cursor: 'pointer',
dataLabels: {
enabled: false
},
showInLegend: true
}
},
series: [{
type: 'pie',
name: 'Pages',
data: [['Using Web', <?php echo $data['tweb'];?>],['Using OPM', <?php echo $data['topm'];?>]]}]
});
</script>
</section>
<?php include 'footer.php';?>