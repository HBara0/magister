<div id="popup_addcontentcat" title="{$lang->editcontentcategory}">
    <form action="#" method="post" id="add_cms/contentcategorieslist_Form" name="add_cms/contentcategorieslist_Form">
        <input type="hidden" name="action" value="do_editcontentcat" />
        <div>
            <div style="display:inline-block; width: 30%;"><strong>{$lang->name}</strong></div>
            <div style="display:inline-block; width: 60%;"><input name="content[name]" type="text" value="{$content[name]}"/></div>
            <input type='hidden' name='content[cmsccid]' value="{$content['cmsccid']}">
        </div>
        <div>
            <div style="display:inline-block; width: 30%;"><strong>{$lang->title}</strong></div>
            <div style="display:inline-block; width: 60%;"><input name="content[title]" type="text" value="{$content[title]}"/></div>
        </div>
        <div>
            <div style="display:inline-block; width: 30%;"><strong>{$lang->isenabled}</strong></div>
            <div style="display:inline-block; width: 60%;"><input name="content[isEnabled]" type="checkbox" value="1" {$checked}/></div>
        </div>
        <div>
            <hr/>
            <div><input type='button' id='add_cms/contentcategorieslist_Button' value='{$lang->savecaps}' class='button'/></div>
            <div style="display:table-row;"> <div style="display:table-cell;"><div id="add_cms/contentcategorieslist_Results"></div></div></div>
        </div>
    </form>
</div>