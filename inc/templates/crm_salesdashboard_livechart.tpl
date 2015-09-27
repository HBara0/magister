var aff = [];
var sales = [];

var chart2 = new ChartComponent();
chart2.setDimensions(6, 6);
chart2.lock()
var name='affs';
var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
var urlparms='';
if(typeof results !='undefined' && results !=null){
urlparms=results;
}
$.post("index.php?module=crm/salesdashboard&action=do_perform_livesales"+urlparms, function (data) {
sales = data['sales'];
aff = data['affiliates'];
var title = data['title'];
var yaxislabel = data['yaxislabel'];
var xaxislabel = data['xaxislabel'];

chart2.setCaption(title);
chart2.setLabels(aff);
chart2.addSeries('yvalues', yaxislabel, sales);
chart2.setYAxis("", {numberHumanize: true});

chart2.unlock();
db.setInterval(function () {
$.post("index.php?module=crm/salesdashboard&action=do_perform_livesales"+urlparms, function (data) {
chart2.updateSeries('yvalues', data['sales']);
});
}, 5000);
});

db.addComponent(chart2);
