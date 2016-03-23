<h3>{$lang->contentcategories}</h3>
<form action='index.php?module=cms/contentcategorieslist' method="post">
    <div style="float: right;"><a href="#popup_addcontentcat" id="showpopup_addcontentcat" class="showpopup"><button type="button">{$lang->addcontetncat}</button></a></div>
    <table class="datatable" width="100%">
        <thead>
            <tr>
                <th width="40%">{$lang->name}</th>
                <th  width="40%">{$lang->title}</th>
            </tr>
            {$filters_row}
        </thead>
    </table>
    <table class="datatable" width="100%">
        <tbody>
            {$cms_contentslist}
        </tbody>
    </table>
</form>
<div style="width:40%; float:left; margin-top:20px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
    </form></div>
{$addcontentcat}