<div id="popup_deletemenuitem" title="{$lang->deletemenuitem}">
    <form action="#" method="post" id="perform_cms/listmenu_Form" name="perform_products/types_Form">
        <input type="hidden" name="action" value="do_deletemenuitem" />
        <input type="hidden" name="todelete" value="{$id}" />
        <div>{$lang->suredelete}</div>
        <input type='button' id='perform_cms/listmenu_Button' value='{$lang->yes}' class='button'/>
        <div id="perform_cms/listmenu_Results"></div>
    </form>
</div>