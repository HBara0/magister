<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillvisitreport}</title>
        {$headerinc}
        <script src="{$core->settings[rootdir]}/js/profiles_marketintelligence.js" type="text/javascript"></script>
        <script language="javascript">
            $(function () {
                /*Refresh timeline on adding MI data (after save)*/
                $(document).on('click', "input[id^='perform_parsetimeline']", function () {
                    // $(".timeline_container").find('.timeline_entry:eq( 2 )').first().effect("highlight", {color: '#D6EAAC'}, 1500); //to be improved later
                    //   setTimeout(function() {
                    sharedFunctions.requestAjax("post", "index.php?module=crm/fillvisitreport&stage=visitdetails&identifier=" + $('#identifier') + "&action=parsemitimeline", "identifier=" + $('#identifier').val(), 'customermktdata', 'customermktdata', true);
                    //  }, 1000
                    //       );

                });
                sharedFunctions.requestAjax("post", "index.php?module=crm/fillvisitreport&stage=visitdetails&identifier=" + $('#identifier') + "&action=parsemitimeline", "identifier=" + $('#identifier').val(), 'customermktdata', 'customermktdata', true);

                setInterval(function () {
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }

                    var id = "save_crm/fillvisitreport_Form".split("_");

                    if($("form[id='save_crm/fillvisitreport_Form']").find("textarea:enabled[value!='']").length > 0) {
                        var formData = $("form[id='save_crm/fillvisitreport_Form']").serialize();
                        sharedFunctions.requestAjax("post", "index.php?module=crm/fillvisitreport&action=autosave", formData, "save_crm/fillvisitreport_Results", "save_crm/fillvisitreport_Results");
                    }

                }, 300000); // 300000 5 minutes
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->fillvisitreport} - {$lang->visitdetails}</h1>
            <form action="index.php?module=crm/fillvisitreport&stage=competition" method="post" id="save_crm/fillvisitreport_Form">
                <input type="hidden" name="identifier" value="{$identifier}" id="identifier">
                {$visitdetails_fields}
                <div>
                    <div class="thead">{$lang->detailmktintelbox} {$addmarketdata_link}</div>
                    <div id="customermktdata">{$visitdetails_fields_mktidata}</div>
                </div>
                <div align="center"><input type="button" value="{$lang->prev}" class="button" onclick="goToURL('index.php?module=crm/fillvisitreport&identifier={$identifier}')"> <input type="submit" value="{$lang->next}" class="button"></div>
                <input type="button" id="perform_parsetimeline" style="display:none;"/>
            </form>
            {$popup_createbrand}
            {$popup_marketdata}
        </td>
    </tr>
    {$footer}
</body>
</html>