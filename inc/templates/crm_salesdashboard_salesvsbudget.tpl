<html>
    <body>
        <script>
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
                chart3.addSeries("revenue", "Revenue", sales, {
                    numberPrefix: "$"
                });
                chart3.addYAxis(linechartlabel, linechartlabel + "%", {
                    numberSuffix: "%"
                });
                chart3.addSeries(linechartlabel, linechartlabel + '%', budget, {
                    numberSuffix: "%",
                    yAxis: linechartlabel,
                    seriesDisplayType: "line"
                });
                chart3.setYAxis("Revenue", {numberPrefix: "$", numberHumanize: true});
                chart3.unlock();
            });
        </script>
    </body>
</html>