<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->aro}</title>
        {$headerinc}
        <script src="js/aro_managedocuments.js" type="text/javascript"></script>
    </head>
    <body>
    <tr>
        {$header}
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>{$lang->managedoumentsequence} </h1>

            <div class="accordion">
                <form name="perform_aro/managearodouments_Form" id="perform_aro/managearodouments_Form"  action="#" method="post">
                    {$aro_managedocuments_orderident}
                    {$partiesinformation}
                    {$aro_ordercustomers}
                    {$aro_netmarginparms}
                    {$aro_productlines}
                    {$actualpurchase}
                    {$aro_audittrail}
                    <input type="submit" class="button" id="perform_aro/managearodouments_Button" value="{$lang->save}"/>
                </form>
                <div id="perform_aro/managearodouments_Results"></div>
            </div>

        </td>
    </tr>
</body>
</html>