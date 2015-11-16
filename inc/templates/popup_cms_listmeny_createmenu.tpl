<div id="popup_createmenu"  title="{$lang->createmenu}">
    <form name='perform_cms/managemenu_Form' id="perform_cms/managemenu_Form" method="post">
        <input type="hidden" id="action" name="action" value="{$actioname}" />
        <input type="hidden" id="action" name="menu[cmsmid]" value="{$menuar['cmsmid']}" />
        <div style="display:table-row">
            <div style="display:table-cell; width:90px;">{$lang->menutitle}</div>
            <div style="display:table-cell"><input name="menu[title]" type="text" value='{$menuar['title']}' size="50" maxlength="30"></div>
        </div>

        <div style="display:table-row;">
            <div style="display:table-cell;width:90px; vertical-align:middle;">{$lang->menudesc}</div>
            <div style="display:table-cell; margin-top:5px;"><textarea name="menu[description]" cols="50" rows="3">{$menuar['description']}</textarea></div>
        </div>
        <hr>
        <div style="display:table-row">
            <div style="display:table-cell">
                <input type="button" id="perform_cms/managemenu_Button" class="button" value="{$lang->add}"/>
                <input type="reset"  class="button" value="{$lang->reset}"/>
            </div>
        </div>

    </form>

    <div id="perform_cms/managemenu_Results" ></div>
</div>