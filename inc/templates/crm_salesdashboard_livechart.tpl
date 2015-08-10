<script>
    var aff = [];
    var sales = [];

    $.post("index.php?module=crm/salesdashboard&action=do_perform_livesales", function (data) {

        sales = data['sales'];
        aff = data['affiliates'];
        var title = data['title'];
        var yaxislabel = data['yaxislabel'];
        var xaxislabel = data['xaxislabel'];
        //    StandaloneDashboard(function(db) {
        //  var chart = new ChartComponent("hashtags");
        // chart.setDimensions(8, 6);

        chart2.setCaption(title);
        chart2.setLabels(aff);
        chart2.addSeries('yvalues', yaxislabel, sales);
        chart2.setYAxis("", {numberHumanize: true});
        // db.addComponent(chart);
        chart2.unlock();

        db.setInterval(function () {
            $.post("index.php?module=crm/salesdashboard&action=do_perform_livesales", function (data) {
                chart2.updateSeries('yvalues', data['sales']);
            });
        }, 1500);

        // });
    });
</script>