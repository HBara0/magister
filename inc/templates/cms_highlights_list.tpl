<h3>{$lang->highlightslist}</h3>
<form action="index.php?module=cms/managehighlight" method="post" enctype="multipart/form-data" name="cms_addnews_Form" id="cms_addnews_Form" target="uploadFrame">
    <div style="float: right;"><a href="{$core->settings['rootdir']}/index.php?module=cms/managehighlight" target="_blank"><button type="button">{$lang->addhighlight}</button></a></div>

    <table class="datatable" width="100%">
        <thead>
            <tr>
                <th width="40%">{$lang->title}</th>
                <th width="40%">{$lang->type}</th>
                <th width="10%">{$lang->isenabled}</th>
                <th width="10%"></th>
            </tr>
            {$filters_row}
        </thead>
        <tbody>
            {$highlights_rows}
        </tbody>
    </table>
</form>
<div style="width:40%; float:left; margin-top:20px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
    </form></div>