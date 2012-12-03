<div id="popup_addrepresentative" title="{$lang->addnewrepresentative}">
	<br />
    <form action="#" method="post" id="add_representative_{$core->input[module]}_Form" name="add_representative_{$core->input[module]}_Form">
    <input type="hidden" name="action" value="do_add_representative">
     <table cellpadding='0' cellspacing='0' width='100%'>
        <tr>
            <td width="40%"><strong>{$lang->representativename}</strong></td><td><input type='text' id='repName' name='repName' tabindex="1"/></td>
        </tr>
        <tr>
            <td><strong>{$lang->email}</strong></td><td><input type='text' id='email' name='repEmail' tabindex="2"/> <span id="repEmail_Validation"></span></td>
        </tr>
             <tr>
            <td><strong>{$lang->phone}</strong></td><td><input name="countrycode" type="text" size="3"><input name="area" type="text" size="4"><input type='text' id='phone' name='repPhone' tabindex="2"/> <span id="repphone_Validation"></span></td>
        </tr>
       {$entity_field_row}
          <td colspan="2" align="left">
         		<hr />
                <input type='button' id='add_representative_{$core->input[module]}_Button' value='{$lang->savecaps}' class='button'/>
                <div id="add_representative_{$core->input[module]}_Results"></div>
            </td>
        </tr>
        </table>
    </form>
</div>