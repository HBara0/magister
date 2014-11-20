<div id="popup_deleterate" title="{$lang->suredeletebudget}">

    <form id="perform_budgeting/listfxrates_Form" name="perform_budgeting/listfxrates_Form"   method="post">
        <input type="hidden" id="action" name="action" value="do_deleterate" />
        <input type="hidden" id="action" name="bfxid" value="{$bfxid}" />

        <table width="100%">
            <tr>
                <td>{$fromcur_obj->get()[name]} TO {$tocur_obj->get()[name]} ?</td>
            </tr>
            <tr>
                <td><hr /> <input type='submit' id='perform_budgeting/listfxrates_Button' value='{$lang->yes}' class='button'/></td>
            </tr>
        </table>
    </form>
    <div id="perform_budgeting/listfxrates_Results" ></div>
</div>