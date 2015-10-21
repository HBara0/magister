<div id="popup_editrepresentative" title="{$lang->editrepresentative}">
    <form name='perform_profiles/representativeslist_Form' id="perform_profiles/representativeslist_Form" method="post">
        <input type="hidden" id="action" name="action" value="do_edit" />
        <input type="hidden" id="rpid" name="rpid" value="{$rpid}" />
        <table>
            <tr>
                <td><strong>{$lang->name}:</strong></td><td><input type="text" id="name" name="name" value="{$representative[name]}"  autocomplete="off" /></td>
            </tr>
            <tr>
                <td>
                    {$lang->companies}:
                </td>
                <td>
                    <table>
                        {$companieslist}
                        <tr>
                        <div>
                            <img src="{$core->settings['rootdir']}/images/add.gif" style="cursor: pointer" id="ajaxaddmore_profiles/representativeslist_entity_{$rownum}" alt="Add">Add more fields
                            <input id="numrows_entity" name="numrows_entity" type="hidden" value="{$rownum}">
                        </div>
            </tr>
        </table>
        </td>
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
            <td colspan="4"><hr /><input type='button' id='perform_profiles/representativeslist_Button' value='{$lang->savecaps}' class='button'/></td>
        </tr>
        </table>
    </form>
    <div id="perform_profiles/representativeslist_Results" ></div>
</div>