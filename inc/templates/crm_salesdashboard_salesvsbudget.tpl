
var chart3 = new ChartComponent();
chart3.setDimensions(6, 6);
chart3.lock();

var aff = [];
var sales = [];
var budget = [];
var totalactual = 0;
var totalbudget = 0;
var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
var urlparms='';
if(typeof results !='undefined' && results !=null){
urlparms=results;
}
$.post("index.php?module=crm/salesdashboard&action=do_perform_combinedbudgetsales"+urlparms, function(data) {
sales = data['sales'];
budget = data['budget'];
aff = data['affiliates'];
var title = data['title'];
var linechartlabel = data['linechartlabel'];

chart3.setCaption(title);
chart3.setLabels(aff);

chart3.addSeries("actual", "Actual", sales, {numberPrefix: "$"});
chart3.addSeries(linechartlabel, linechartlabel, budget, {numberPrefix: "$", yAxis: linechartlabel, seriesDisplayType: "line"});
chart3.setYAxis("", {numberPrefix: "$", numberHumanize: true});

$.each(sales, function( index, value ){
totalactual += parseFloat(value);
});
chart3.addComponentKPI("totalactual", {
caption: "Total Actual",
value: totalactual,
numberPrefix: " $",
numberHumanize: true
});

$.each(budget, function( index, value ){
totalbudget += parseFloat(value);
});
chart3.addComponentKPI("totalbudget", {
caption: "Total Budget",
value: totalbudget,
numberPrefix: " $",
numberHumanize: true
});
chart3.unlock();

});
db.addComponent(chart3);




