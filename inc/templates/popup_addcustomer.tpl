<div id="popup_addcustomer" title="{$lang->addnewcustomer}">
    <form action="#" method="post" id="add_newcustomer_reporting/fillreport_Form" name="add_newcustomer_reporting/fillreport_Form">
        <input type="hidden" name="action" value="save_newcustomer" />
        <input type="hidden" value="c" name="type">
        <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td width="40%"><strong>{$lang->companyname}</strong></td><td><input type="text" id="companyName" name="companyName" tabindex="1"/></td>
            </tr>
            <tr>
                <td><strong>{$lang->affiliate}</strong></td><td>{$affiliates_list}</td>
            </tr>
            <tr>
                <td><strong>{$lang->country}</strong></td><td>{$countries_list}</td>
            </tr>
            <tr>
                <td><strong>{$lang->contactperson}</strong></td><td><input type="text" id="repName" name="repName" tabindex="4"/></td>
            </tr>
            <tr>
                <td><strong>{$lang->email}</strong></td><td><input type="text" id="email" name="repEmail" tabindex="5"/> <span id="repEmail_Validation"></span></td>
            </tr>
            <td colspan="2" align="left">
                <hr />
                <input type='button' id='add_newcustomer_reporting/fillreport_Button' value='{$lang->savecaps}' class='button'/>
                <div id="add_newcustomer_reporting/fillreport_Results"></div>
            </td>
            </tr>
        </table>
    </form>
</div>