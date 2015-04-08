<div id="popup_addsegment" title="{$lang->addasegment}">
    <form id="add_products/segments_Form" name="add_products/segments_Form" action="#" method="post">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
            <input type='hidden' name='segment[psid]' value='{$segment[psid]}'/>
            <td width="10%">{$lang->title}</td>
            <td width="90%"><input type="text" id="title" name="segment[title]" value="{$segment['title']}"/></td>
            </tr>
            <tr><td class="subtitle" width="10%">{$lang->coordinator}</td></tr>
            <tbody id="users_tbody">
                {$users_rows}
            </tbody>
            <tr><td colspan="3"><img src="../images/add.gif" id="addmore_users" alt="{$lang->add}"><input type="hidden" name="users_numrows" id="numrows" value="{$users_counter}"></td><tr>
            <tr>
                <td>{$lang->description}</td><td><textarea cols="30" rows="5" id="description" name="segment[description]">{$segment['description']}</textarea></td>
            </tr>
            <tr>
                <td colspan="2"><input type="button" id="add_products/segments_Button" value="{$lang->add}" class="button" /><input type="reset" value="{$lang->reset}" class="button" />
                    <div id="add_products/segments_Results"></div>
                </td>
            </tr>
        </table>
    </form>
</div>