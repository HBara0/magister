var form = new FormComponent();
form.setDimensions (4, 3);
var chart3 = new ChartComponent();
chart3.setDimensions(6, 6);
chart3.lock();
form.lock();
var aff = [];
var sales = [];
var budget = [];
var totalactual = 0;
var totalbudget = 0;
$.post("index.php?module=crm/salesdashboard&action=do_perform_combinedbudgetsales", function(data) {
sales = data['sales'];
budget = data['budget'];
aff = data['affiliates'];
var aff2d=data['filteraffiliates'];
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


form.setCaption ("Filter by Affiliate");
form.addMultiSelectField ('affiliate', 'Select Affiliate', aff);
form.unlock();
form.onApplyClick (function(params) {
$.post("index.php?module=crm/salesdashboard&action=do_perform_combinedbudgetsales&affid="+params['affiliate']['text'], function (data) {
chart3.lock();
chart3.clearChart();
chart3.setLabels(data['filteraffiliates']);
chart3.addSeries("actual", "Actual", data['sales'], {numberPrefix: "$"});
chart3.addSeries(data['linechartlabel'], data['linechartlabel'], data['budget'], {numberPrefix: "$", yAxis: data['linechartlabel'], seriesDisplayType: "line"});
chart3.unlock();});
});

});

db.addComponent(chart3);
db.addComponent(form);



