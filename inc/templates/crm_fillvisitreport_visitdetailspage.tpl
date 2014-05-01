<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillvisitreport}</title>
        {$headerinc}
        <script language="javascript">
            $(function() {
                setInterval(function() {
                    if (sharedFunctions.checkSession() == false) {
                        return;
                    }

                    var id = "save_crm/fillvisitreport_Form".split("_");

                    if ($("form[id='save_crm/fillvisitreport_Form']").find("textarea:enabled[value!='']").length > 0) {
                        var formData = $("form[id='save_crm/fillvisitreport_Form']").serialize();
                        sharedFunctions.requestAjax("post", "index.php?module=crm/fillvisitreport&action=autosave", formData, "save_crm/fillvisitreport_Results", "save_crm/fillvisitreport_Results");
                    }
                }, 1000); // 300000 5 minutes
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->fillvisitreport} - {$lang->visitdetails}</h3>
            <form action="index.php?module=crm/fillvisitreport&stage=competition" method="post" id="save_crm/fillvisitreport_Form">
                <input type="hidden" name="identifier" value="{$identifier}">
                {$visitdetails_fields}
                <div align="center"><input type="button" value="{$lang->prev}" class="button" onclick="goToURL('index.php?module=crm/fillvisitreport&identifier={$identifier}')"> <input type="submit" value="{$lang->next}" class="button"></div>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>