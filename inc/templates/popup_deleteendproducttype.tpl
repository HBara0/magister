<div id="popup_deleteendproducttype" title="{$lang->deleteendproducttype}">
    <form action="#" method="post" id="perform_products/types_Form" name="perform_products/types_Form">
        <input type="hidden" name="action" value="delete_endproducttype" />
        <input type="hidden" name="todelete" value="{$id}" />
        <div>{$lang->suredelete}</div>
        <input type='button' id='perform_products/types_Button' value='{$lang->yes}' class='button'/>
        <div id="perform_products/types_Results"></div>
    </form>
</div>