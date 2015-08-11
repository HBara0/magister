<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->salesdashboard}</title>
        {$headerinc}

        <link rel="stylesheet" type="text/css" href="{$core->settings[rootdir]}/inc/razorflow/razorflow_js/dashboard_quickstart/css/razorflow.min.css">
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
        <script type="text/javascript" src="{$core->settings[rootdir]}/inc/razorflow/razorflow_js/dashboard_quickstart/js/razorflow.min.js"></script>
        <script type="text/javascript" src="{$core->settings[rootdir]}/inc/razorflow/razorflow_js/dashboard_quickstart/js/razorflow.devtools.min.js"></script>
        <script>
            var chart = new ChartComponent("chart");
            var chart2 = new ChartComponent("hashtags");
            var chart3 = new ChartComponent("hashtags");

            rf.StandaloneDashboard(function(db) {
                chart2.setDimensions(6, 6);
                chart2.lock();
                db.addComponent(chart2);

                chart.setDimensions(6, 6);
                chart.lock();
                db.addComponent(chart);

                chart3.setDimensions(6, 6);
                chart3.lock();
                db.addComponent(chart3);

            });
        </script>
        {$livechart}{$drilldown}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->salesdashboard}</h1>
            <div id="dbTarget" style="position:relative;" class="rf"></div>
        </td>
    </tr>
    {$footer}
</body>
</html>