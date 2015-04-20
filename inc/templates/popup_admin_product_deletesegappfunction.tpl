<div id="popup_deleteappfunc" title="{$lang->deletesegapfunc}">
    <form action="#" method="post" id="perform_products/functions_Form" name="perform_products/functions_Form">
        <input type="hidden" name="action" value="deleteappfunc" />
        <input type="hidden" name="safid" value="{$safid}" />

        <div>
            <h2>{$lang->areyousure}</h2>
        </div>
        <input type='button' id='perform_products/functions_Button' value='{$lang->yes}' class='button'/>
        <div id="perform_products/functions_Results"></div>
    </form>
</div>