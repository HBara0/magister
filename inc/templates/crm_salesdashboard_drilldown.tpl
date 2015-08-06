<html>
    <body>
        <script>$.post("index.php?module=crm/salesdashboard&action=do_perform_totalsalesperyear", function(data) {
                var years = data['years'];
                var salesperyear = [];
                var salesperquarter = {};
                var salespermonth ={};
                var yearData ={};

                var title = data['title'];
                var yaxislabel = data['yaxislabel'];
                var xaxislabel = data['xaxislabel'];

                salesperyear = data['salesperyear'];
                years.forEach(function(year) {
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

                chart.setCaption("Annual Sales Summary (" + years[0] + " - " + years[2] + ")");
                chart.setLabels(years);
                chart.addSeries(xaxislabel, yaxislabel, salesperyear);
                chart.setYAxis("Sales", {
                    numberPrefix: "$",
                    numberHumanize: true
                });
                var selectedYear;
                var labelsForQuarters = {
                    "Q1": ["January", "February", "March"],
                    "Q2": ["April", "May", "June"],
                    "Q3": ["July", "August", "September"],
                    "Q4": ["October", "November", "December"]
                };
                //yearData

                chart.unlock();
                chart.addDrillStep(function(done, params, updatedComponent) {
                    var label = selectedYear = params.label;
                    if(typeof label == 'string') {
                        label = parseInt(label, 10);
                    }
                    updatedComponent.setLabels(["Q1", "Q2", "Q3", "Q4"]);
                    updatedComponent.addSeries("sales", "Sales", yearData[label].data);
                    done();
                });

                chart.addDrillStep(function(done, params, updatedComponent) {
                    var label = params.label;
                    updatedComponent.setLabels(labelsForQuarters[label]);
                    updatedComponent.addSeries("sales", "Sales", yearData[selectedYear][label]);
                    done();
                });
            });
        </script>
    </body>
</html>