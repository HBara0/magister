<div id="popup_editrepresentative" title="{$lang->editrepresentative}">
    <form name='change_profiles/representativeslist_Form' id="change_profiles/representativeslist_Form" method="post">
        <input type="hidden" id="action" name="action" value="do_edit" />
        <input type="hidden" id="rpid" name="rpid" value="{$rpid}" />
        <table>
            <tr>
                <td><strong>{$lang->name}:</strong></td><td><input type="text" id="name" name="name" value="{$representative[name]}"  autocomplete="off" /></td>
            </tr>
            <tr>
                <td><strong>{$lang->companyname}:</strong></td><td><input id="{$type}_1_QSearch" autocomplete="off" type="text" value="{$entity[companyName]}"><input size="3" id="{$type}_1_id_output"  disabled="disabled" type="text" value="{$entity[eid]}"><input id="{$type}_1_id" name="eid"  value="{$entity[eid]}" type="hidden"><div id="searchQuickResults_1" class="searchQuickResults" style="display: none;"></div></td>
            </tr>
            <tr>
                <td><strong>{$lang->position}:</strong></td><td>{$representative[positions]}</td>
            </tr>
            <tr>
                <td><strong>{$lang->segments}:</strong></td><td>{$representative[segments]}</td>
            </tr>
            <tr>
                <td><strong>{$lang->email}:</strong></td><td><input type="text" id="email" name="email" value="{$representative[email]}" autocomplete="off" /> <span id="email_Validation"></span></td>
            </tr>
            <tr>
                <td><strong>{$lang->telephone}:</strong></td><td><input type="text" id="phone_intcode" name="phone_intcode" size="3" maxlength="3" accept="numeric" value="{$representative[phone][intcode]}" /> <input type="text" id="phone_areacode" name="phone_areacode" size='4' maxlength="4" accept="numeric" value="{$representative[phone][areacode]}" /> <input type="text" id="phone_number" name="phone_number" accept="numeric" value="{$representative[phone][number]}" /></td>
            </tr>
            <tr>
                <td colspan="4"><hr /><input type='button' id='change_profiles/representativeslist_Button' value='{$lang->savecaps}' class='button'/></td>
            </tr>
        </table>
    </form>
    <div id="change_profiles/representativeslist_Results" ></div>
</div>