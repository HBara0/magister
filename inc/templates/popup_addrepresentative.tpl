<div id="popup_addrepresentative" title="{$lang->addnewrepresentative}">
	<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p>{$lang->addrepresentative_note}</p></div>
    <form action="#" method="post" id="add_representative_{$core->input[module]}_Form" name="add_representative_{$core->input[module]}_Form">
    <input type="hidden" name="action" value="do_add_representative" />
     <table cellpadding='0' cellspacing='0' width='100%'>
        <tr>
            <td width="40%"><strong>{$lang->representativename}</strong></td><td><input type='text' required="required" id='repName' name='repName' tabindex="1"/></td>
        </tr>
        <tr>
            <td><strong>{$lang->email}</strong></td><td><input type='email' required="required" accept="email" id='repEmail' name='repEmail' tabindex="2"/> <span id="repEmail_Validation"></span></td>
        </tr>
             <tr>
            <td>{$lang->telephone}</td><td><input name="repTelephone[intcode]" accept="numeric" type="text" size="3" /><input name="repTelephone[areacode]" accept="numeric" type="text" size="4" /><input type='text' accept="numeric" id='repTelephone_number' name='repTelephone[number]' tabindex="2"/></td>
        </tr>
       {$entity_field_row}
          <td colspan="2" align="left">
         		<hr />
                <input type='submit' id='add_representative_{$core->input[module]}_Button' value='{$lang->savecaps}' class='button'/>
                <div id="add_representative_{$core->input[module]}_Results"></div>
            </td>
        </tr>
        </table>
    </form>
</div>