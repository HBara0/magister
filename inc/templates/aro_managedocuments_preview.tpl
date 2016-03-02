<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->aro} {$arodocument_title}</title>
        {$headerinc}
        <script src="{$core->settings[rootdir]}/js/jquery.sparkline.min.js" type="text/javascript"></script>
        <script src="{$core->settings[rootdir]}/js/jquery.populate.min.js" type="text/javascript"></script>
        <script src="{$core->settings[rootdir]}/js/aro_managedocuments.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(function() {
                /** This code runs when everything has been loaded on the page */
                /* Inline sparklines take their values from the contents of the tag */
                $('.inlinesparkline').sparkline();
                /* Use 'html' instead of an array of values to pass options
                 to a sparkline with data in the tag */
                $('.inlinebar').sparkline('html', {type: 'bar', barColor: 'red'});
            });
        </script>
    </head>
    <body>
    <tr>
        {$header}
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>Aro Document</h1>
            <div style="display:inline-block;width:60%">
                {$arostatus}
                {$arodocument_header}  {$rejected_watrermark}</div>
            <div style="display:inline-block;width:30%">
                {$postatus}
            </div>
            <div class="accordion">
                <form name="perform_aro/managearodouments_Form" id="perform_aro/managearodouments_Form"  action="#" method="post">
                    {$aro_managedocuments_orderident}
                    {$partiesinformation}
                    {$aro_ordercustomers}
                    {$aro_netmarginparms}
                    {$aro_productlines}
                    {$actualpurchase}
                    {$currentstock}
                    {$comparisonstudy}
                    {$aro_audittrail}
                    {$orderummary}
                    {$totalfunds}
                </form>
                <div id="perform_aro/managearodouments_Results"></div>
                <hr />
                {$takeactionpage}
                {$approvalchain}
            </div>
        </td>
    </tr>
    <!-- Start Tour -->
    {$helptour_output}


</body>
</html>