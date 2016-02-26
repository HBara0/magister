<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listofleaves}</title>
        {$headerinc}
        <script language="javascript">
            $(function() {
                $('#moderationtools').change(function() {
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }

                    if($(this).val().length > 0) {
                        var formData = $("form[id='moderation_attendance/listleaves_Form']").serialize();
                        var url = "index.php?module=attendance/listleaves&action=do_moderation";

                        sharedFunctions.requestAjax("post", url, formData, "moderation_attendance/listleaves_Results", "moderation_attendance/listleaves_Results");
                    }
                });
            });
        </script>
        <script type="text/javascript" src="{$core->settings[rootdir]}/js/tableExport.min.js"></script>
        <script type="text/javascript" src="{$core->settings[rootdir]}/js/jquery.base64.min.js"></script>
    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->listofleaves}</h1>
            <form action="#" method="post" id="moderation_attendance/listleaves_Form" name="moderation_attendance/listleaves_Form" style="margin-bottom: 0px;">
                <table class="datatable">
                    <thead>
                        <tr>
                            <th style="width:17%;">{$lang->employeename} <a href="{$sort_url}&amp;sortby=employeename&amp;or19der=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=employeename&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th style="width:17%;">{$lang->daterequested} <a href="{$sort_url}&amp;sortby=daterequested&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=daterequested&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th style="width:17%;">{$lang->fromdate} <a href="{$sort_url}&amp;sortby=fromdate&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=fromdate&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th style="width:17%;">{$lang->todate} <a href="{$sort_url}&amp;sortby=till&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=till&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th style="width:17%;">{$lang->leavetype} <a href="{$sort_url}&amp;sortby=type&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=type&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th style="width:10%;">{$lang->workingdays} <a href="{$sort_url}&amp;sortby=workingDays&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=workingDays&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th style="width:5%;">&nbsp;</th>
                        </tr>
                        {$filters_row}
                    </thead>
                </table>
                <form action="#" method="post" id="moderation_attendance/listleaves_Form" name="moderation_attendance/listleaves_Form" style="margin-bottom: 0px;">
                    <table class="datatable" id="results_table">
                        <thead><tr class="dummytrow"><th style="width:19%;"></th><th style="width:19%;"></th><th style="width:19%;"></th><th style="width:19%;"></th><th style="width:19%;"></th><th colspan="2" style="width:5%;"></th></tr></thead>
                        <tbody>
                            {$requestslist}
                        </tbody>
                    </table>
                    <a style="float:right" title="{$lang->exporttoexcel}" onClick ="$('#results_table').tableExport({type:'excel',escape:'false'});"><img src="./images/icons/xls.gif"/></a>

                </form>
                <div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
        </td>
    </tr>
    {$footer}
</body>
</html>