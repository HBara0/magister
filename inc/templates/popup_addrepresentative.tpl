<script>
    $('a[title]').qtip({style: {classes: 'ui-tooltip-green ui-tooltip-shadow'}, show: {event: 'focus mouseenter', solo: true}, hide: 'unfocus mouseleave', position: {viewport: $(window)}});
</script>
<div id="popup_addrepresentative" title="{$lang->addnewrepresentative}">
    <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p>{$lang->addrepresentative_note}</p></div>
    <form action="#" method="post" id="add_representative_{$core->input[module]}_Form" name="add_representative_{$core->input[module]}_Form">
        <input type="hidden" name="action" value="do_add_representative" />
        <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td width="40%"><strong>{$lang->representativename}</strong></td><td><input type='text' required="required" id='repName' name='repName' tabindex="1"/></td>
            </tr>
            <tr>
                <td><strong>{$lang->email}</strong></td><td><input type='email' id='repEmail' name='repEmail' tabindex="2" placeholder="name@example.com" /></td>
            </tr>
            <tr>
                <td width="40%">{$lang->position}</td>
                <td>{$positions_selectlist}</td>
            </tr>
            <tr>
                <td width="40%">{$lang->issupportive} <a href="#" title="{$lang->issuportivedescription}" class=""><img src="./images/icons/question.gif" /></a></td>
                <td><select name="isSupportive"><option value=""></option><option value="1">{$lang->yes}</option><option value="0">{$lang->no}</option></select>
                </td>
            </tr>
            <tr>
                <td><strong>{$lang->telephone}</strong></td><td>{$countries_phonecodes}<input name="repTelephone[areacode]" accept="numeric" type="text" size="4" /><input type='text' accept="numeric" id='repTelephone_number' name='repTelephone[number]' tabindex="2"/></td>
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