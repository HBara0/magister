<div id="popup_deleteapp"  title="{$lang->deleteapp}">
    <form action="#" method="post" id="perform_products/applications_Form" name="perform_products/applications_Form">
        <input type="hidden" name="action" value="do_delete" />
        <input type="hidden" name='psaid' value='{$psaid}'/>
        <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td width="40%"><strong>{$lang->areyousuredeleteapp}</strong></td>
            </tr>
            <tr>
                <td colspan="2" align="left">
                    <hr />
                    <input type='button' id='perform_products/applications_Button' value='{$lang->yes}' class='button'/>
                    <div id="perform_products/applications_Results"></div>
                </td>
            </tr>
        </table>
    </form>
</div>