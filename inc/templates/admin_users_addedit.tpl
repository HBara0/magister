<h1>{$pagetitle}</h1>
<form action="#" method="post" id="perform_users/{$actiontype}_Form" name="perform_users/{$actiontype}_Form">
    {$uidfield}
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <strong>{$lang->firstname}</strong>
            </td>
            <td>
                <input type="text" id="firstName" name="firstName" value="{$user[firstName]}" tabindex="7"/> <strong>{$lang->lastname}</strong> <input type="text" id="lastName" name="lastName" value="{$user[lastName]}" tabindex="9"/>
            </td>
        </tr>
        <tr>
            <td><strong>{$lang->email}</strong></td>
            <td><input type="text" id="email" name="email" value="{$user[email]}" tabindex="4"/> <span id="email_Validation"></span>

            </td>
        </tr>
        <tr>
            <td><strong>{$lang->password}</strong></td>
            <td><input type="password" id="password" name="password"  tabindex="2"/>
                <strong>{$lang->confirmpassword}</strong><input type="password" id="password2" name="password2"  tabindex="3"/>
            </td>
        </tr>
        <tr>
            <td><strong>{$lang->program}</strong></td>
            <td>
                {$programs_list}
            </td>
        </tr>
        <tr>
            <td><strong>{$lang->usergroup}</strong></td><td>{$usergroups_list}</td>
        </tr>
        <tr>
            <td colspan="2" align="left">
                <input class="btn btn-success" type="button" value="{$lang->$actiontype}" id="perform_users/{$actiontype}_Button"  tabindex="26"/>
                <div id="perform_users/{$actiontype}_Results"></div>
            </td>
        </tr>
    </table>
</form>