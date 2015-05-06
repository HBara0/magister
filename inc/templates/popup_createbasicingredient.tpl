<div id="popup_createbasicingredient" title="{$lang->createbasicingredient}">
    <form action="#" method="post" id="add_{$module}/{$modulefile}_Form" name="add_{$module}/{$modulefile}_Form">
        <input type="hidden" name="action" value="do_addbasicingredient" />
        <div>
            <div style="display:inline-block; width: 25%;"><strong>{$lang->title}</strong></div>
            <div style="display:inline-block; width: 75%;">
                <input name="basicingredient[biid]" type="hidden" />
                <input name="basicingredient[title]" type="text" required="required"/>
            </div>
        </div>
        <div>
            <div style="display: inline-block; width: 25%;"><strong>{$lang->description}</strong></div>
            <div style="display: inline-block; width: 75%;">
                <textarea style="vertical-align:top;" name="basicingredient[description]" id="basicingredient_description" cols="20" rows="5"></textarea>
            </div>
            <div>
                <hr />
                <div><input type='button' id='add_{$module}/{$modulefile}_Button' value='{$lang->savecaps}' class='button'/></div>
                <div style="display:table-row;"> <div style="display:table-cell;"><div id="add_{$module}/{$modulefile}_Results"></div></div></div>
            </div>
    </form>
</div>