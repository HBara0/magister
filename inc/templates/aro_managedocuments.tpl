<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->aro} {$arodocument_title}</title>
        {$headerinc}
        <script src="{$core->settings[rootdir]}/js/jquery.populate.min.js" type="text/javascript"></script>
        <script src="{$core->settings[rootdir]}/js/aro_managedocuments.js" type="text/javascript"></script>
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
                    {$aro_audittrail}
                    {$orderummary}
                    {$totalfunds}
                    {$takeactionpage}
                    {$approvalchain}
                    <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;width:30%">
                        {$lang->finalizedemail}
                    </div>
                    <input type="checkbox" value="1" name="isFinalized" {$checked[aroisfinalized]}/>{$lang->finalize}<br/><br/>
                    <input type="submit" class="button" id="perform_aro/managearodouments_Button" value="{$lang->savecaps}"/>
                    {$deletebutton}
                </form>
                <div id="perform_aro/managearodouments_Results"></div>
                <hr />

            </div>
        </td>
    </tr>
    <!-- Start Tour -->
    {$helptour_output}


</body>
</html>