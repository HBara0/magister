<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->salesdashboard}</title>
        {$headerinc}
        <link rel="stylesheet" type="text/css" href="{$core->settings[rootdir]}/inc/razorflow/razorflow_js/files/css/razorflow.min.css">
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
        <script type="text/javascript" src="{$core->settings[rootdir]}/inc/razorflow/razorflow_js/files/js/razorflow.min.js"></script>
        <script>
            rf.StandaloneDashboard(function(db) {
            {$livechart}{$drilldown}{$combinedsalesbudget}

                var form = new FormComponent();
                form.setDimensions(4, 3);
                form.lock();
                $.ajax({
                    type: "GET",
                    url: "index.php?module=crm/salesdashboard&action=get_affiliates",
                    success: function(data) {
                        var obj = JSON.parse(data)
                        var obj = JSON.parse(data);
                        form.setCaption("Filter by Affiliate");
                        form.unlock();
                        form.addMultiSelectField('affiliate', 'Select Affiliate', obj.affiliates);

                        form.onApplyClick(function(params) {
                            var url = window.location.href;
                            if(url.indexOf('?') > -1) {
                                url += '&affs=' + params['affiliate']['text'];
                            } else {
                                url += '?affs=' + params['affiliate']['text'];
                            }
                            window.location.href = url;
                            //   $.post("index.php?module=crm/salesdashboard&action=do_perform_combinedbudgetsales&affid=" + params['affiliate']['text'], function(data) {
                            //       chart3.lock();
                            //       chart3.clearChart();
                            //       chart3.setLabels(data['filteraffiliates']);
                            //        chart3.addSeries("actual", "Actual", data['sales'], {numberPrefix: "$"});
                            //        chart3.addSeries(data['linechartlabel'], data['linechartlabel'], data['budget'], {numberPrefix: "$", yAxis: data['linechartlabel'], seriesDisplayType: "line"});
                            //        chart3.unlock();
                            //    });
                        });

                    }
                });
                db.addComponent(form);




            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->salesdashboard} <small>({$dashbboard_currency})</small></h1>
            <div id="dbTarget"></div>
        </td>
    </tr>
    {$footer}
</body>
</html>