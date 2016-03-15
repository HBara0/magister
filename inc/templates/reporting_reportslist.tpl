<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listofreports}</title>
        {$headerinc}
        <script language="javascript">
            $(function() {
                $('#moderationtools').change(function() {
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }

                    if($(this).val().length > 0) {
                        var formData = $("form[id='moderation_reporting/list_Form']").serialize();
                        var url = "index.php?module=reporting/list&action=do_moderation";

                        sharedFunctions.requestAjax("post", url, formData, "moderation_reporting/list_Results", "moderation_reporting/list_Results");
                    }
                });

                $('a[rel]').each(function() {
                    $(this).qtip({
                        overwrite: true,
                        content: {
                            text: "<img src='" + imagespath + "/loading.gif' alt='" + loading_text + "' border='0' /        >",
                            ajax: {once: true, url: 'index.php?module=reporting/list&action=get_status&rid=' + $(this).attr('rel'),}
                        }
                    });
                });
            });
        </script>
    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->listofreports}</h1>
            <form action="#" method="post" id="moderation_reporting/list_Form" name="moderation_reporting/list_Form" style="margin-bottom: 0px;">
                <table class="datatable_basic table table-bordered row-border hover order-column" data-checkonclick=true cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>{$lang->affiliate} <a href="{$sort_url}&amp;sortby=affiliatename&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=affiliatename&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th>{$lang->supplier} <a href="{$sort_url}&amp;sortby=suppliername&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=suppliername&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th>{$lang->quarter} <a href="{$sort_url}&amp;sortby=quarter&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=quarter&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th>{$lang->year} <a href="{$sort_url}&amp;sortby=year&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=year&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th>{$lang->status} <a href="{$sort_url}&amp;sortby=status&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=status&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>

                    <tfoot>
                        <tr>
                            <th>&nbsp;</th>
                            <th>{$lang->affiliate} <a href="{$sort_url}&amp;sortby=affiliatename&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=affiliatename&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th>{$lang->supplier} <a href="{$sort_url}&amp;sortby=suppliername&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=suppliername&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th>{$lang->quarter} <a href="{$sort_url}&amp;sortby=quarter&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=quarter&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th>{$lang->year} <a href="{$sort_url}&amp;sortby=year&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=year&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th>{$lang->status} <a href="{$sort_url}&amp;sortby=status&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=status&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        {$reportslist}
                    </tbody>
                    <tfoot>
                        {$moderationtools}
                    </tfoot>
                </table>
            </form>
            <div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>Per list: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
            <div style="text-align: right; width:50%; float:right; margin-top:0px;" class="smalltext">
                <a href="index.php?module=reporting/list">{$lang->all}</a> | <a href="index.php?module=reporting/list&amp;filterby=status&amp;filtervalue=1">{$lang->finalizedonly}</a> | <a href="index.php?module=reporting/list&amp;filterby=status&amp;filtervalue=0">{$lang->unfinalizedonly}</a> | <a href="index.php?module=reporting/list&amp;filterby=issent&amp;filtervalue=1">{$lang->sentonly}</a>
            </div>
        </td>
    </tr>
    {$footer}
</body>
</html>