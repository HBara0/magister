var chart = new ChartComponent();
chart.setDimensions(6, 6);

var salesperyear = [];
var salesperquarter = {};
var salespermonth ={};
var yearData ={};
var selectedYear;
var labelsForQuarters = {
"Q1": ["January", "February", "March"],
"Q2": ["April", "May", "June"],
"Q3": ["July", "August", "September"],
"Q4": ["October", "November", "December"]
};
var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
var urlparms='';
if(typeof results !='undefined' && results !=null){
urlparms=results;
}
$.post("index.php?module=crm/salesdashboard&action=do_perform_totalsalesperyear"+urlparms, function (data) {
var years = data['years'];
var title = data['title'];
var yaxislabel = data['yaxislabel'];
var xaxislabel = data['xaxislabel'];

salesperyear = data['salesperyear'];
years.forEach(function (year) {
salesperquarter[year] = data[year];
var obj ={};
for(quarter = 1; quarter < 5; quarter++) {
var index = year + "_" + quarter;
salespermonth[index] = data[index];
var qindex = "Q" + quarter;
obj[qindex] = salespermonth[year + "_" + quarter];
}
obj.data = salesperquarter[year];
yearData[year] = obj;
})
chart.unlock()
chart.setCaption(data['title']);
chart.setLabels(years);
chart.addSeries('sales', "Sales", salesperyear);
chart.setYAxis("Sales", {
numberPrefix: "$",
numberHumanize: true
});

});

chart.addDrillStep(function (done, params, updatedComponent) {
var label = selectedYear = params.label;
if(typeof label == 'string') {
label = parseInt(label, 10);
}
updatedComponent.setLabels(["Q1", "Q2", "Q3", "Q4"]);
updatedComponent.addSeries("sales", "Sales", yearData[label].data);
done();
});

chart.addDrillStep(function (done, params, updatedComponent) {
var label = params.label;
updatedComponent.setLabels(labelsForQuarters[label]);
updatedComponent.addSeries("sales", "Sales", yearData[selectedYear][label]);
done();
});

chart.lock()
db.addComponent(chart);