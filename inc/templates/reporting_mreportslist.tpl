<script language="javascript">
    $(function()
    {
        $('#moderationtools').change(function()
        {
            if(sharedFunctions.checkSession() == false)
            {
                return;
            }

            if($(this).val().length > 0)
            {
                var formData = $("form[id='moderation_reporting/listmreports_Form']").serialize();
                var url = "index.php?module=reporting/listmreports&action=do_moderation";

                sharedFunctions.requestAjax("post", url, formData, "moderation_reporting/listmreports_Results", "moderation_reporting/listmreports_Results");
            }
        });
    });
</script>
<h1>{$lang->listofreports}</h1>
<form action="index.php?module=reporting/previewmreport&amp;referrer=generate" method="post" id="moderation_reporting/listmreports_Form" name="moderation_reporting/listmreports_Form" style="margin-bottom: 0px;">
    <table class="datatable">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>{$lang->affiliate} <a href="{$sort_url}&amp;sortby=affiliatename&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=affiliatename&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th>{$lang->supplier} <a href="{$sort_url}&amp;sortby=suppliername&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=suppliername&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th>{$lang->month} <a href="{$sort_url}&amp;sortby=month&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=month&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th>{$lang->year} <a href="{$sort_url}&amp;sortby=year&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=year&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
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
    <a href="index.php?module=reporting/listmreports&amp;filterby=islocked&amp;filtervalue=0">{$lang->unlockedonly} Unlocked Only</a> | <a href="index.php?module=reporting/listmreports&amp;filterby=issent&amp;filtervalue=1">{$lang->sentonly}</a>
</div>