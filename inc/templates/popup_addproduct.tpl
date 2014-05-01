<div id="popup_addproduct" title="{$lang->addnewproduct}">
    <form id='add_newproduct_reporting/fillreport_Form' name='add_newproduct_reporting/fillreport_Form' action='#' method='post'>
        <input type="hidden" name="action" value="save_newproduct" />
        <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td><strong>{$lang->name}</strong></td><td><input type='text' name='name' id='name' tabindex='1' /></td>
            </tr>
            <tr>
                <td><strong>{$lang->generic}</strong></td><td>{$generics_list}</td>
            </tr>
            <tr>    
                <td><strong>{$lang->supplier}</strong></td><td>{$popup_addsupplier_supplierfield}</td>
            </tr>
            <tr><td colspan='2'><hr /><input type='button' id='add_newproduct_reporting/fillreport_Button' value='{$lang->savecaps}' class='button'/>
                    <div id='add_newproduct_reporting/fillreport_Results'></div></td></tr>
        </table>
    </form>
</div>