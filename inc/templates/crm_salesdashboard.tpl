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
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->salesdashboard}</h1>
            <div id="dbTarget"></div>
        </td>
    </tr>
    {$footer}
</body>
</html>