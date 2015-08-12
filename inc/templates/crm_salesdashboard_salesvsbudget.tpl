var chart3 = new ChartComponent();
chart3.setDimensions(6, 6);
chart3.lock();
var aff = [];
var sales = [];
var budget = [];
$.post("index.php?module=crm/salesdashboard&action=do_perform_combinedbudgetsales", function(data) {
sales = data['sales'];
budget = data['budget'];
aff = data['affiliates'];
var title = data['title'];
var linechartlabel = data['linechartlabel'];
// var yaxislabel = data['yaxislabel'];
// var xaxislabel = data['xaxislabel'];
chart3.setCaption(title);
chart3.setLabels(aff);
//   chart3.addSeries(xaxislabel, yaxislabel, sales, {
//       numberPrefix: "$"
//    });
chart3.addSeries("actual", "Actual", sales, {numberPrefix: "$"});
chart3.addSeries(linechartlabel, linechartlabel, budget, {numberPrefix: "$", yAxis: linechartlabel, seriesDisplayType: "line"});
chart3.setYAxis("", {numberPrefix: "$", numberHumanize: true});
chart3.unlock();
});
db.addComponent(chart3);