<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->aro}</title>
        {$headerinc}
        <script src="{$core->settings[rootdir]}/js/jquery.populate.min.js" type="text/javascript"></script>
        <script src="{$core->settings[rootdir]}/js/aro_managedocuments.min.js" type="text/javascript"></script>
    </head>
    <body>
    <tr>
        {$header}
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>Aro Document</h1>

            <div class="accordion">
                <form name="perform_aro/managearodouments_Form" id="perform_aro/managearodouments_Form"  action="#" method="post">
                    {$aro_managedocuments_orderident}
                    {$partiesinformation}
                    {$aro_ordercustomers}
                    {$aro_netmarginparms}
                    {$aro_productlines}
                    {$actualpurchase}
                    {$currentstock}
                    {$aro_audittrail}
                    {$orderummary}
                    {$totalfunds}
                    {$approvalchain}

                    <input type="submit" class="button" id="perform_aro/managearodouments_Button" value="{$lang->savecaps}"/>
                </form>
                <div id="perform_aro/managearodouments_Results"></div>
                <hr />
                {$takeactionpage}
            </div>

        </td>
    </tr>
    <!-- Start Tour -->
    {$helptour_output}


</body>
</html>