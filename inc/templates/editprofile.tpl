<span style="margin-right:10px;"><a href="{$_SERVER[HTTP_REFERER]}">&laquo; Go back</a></span></td>

<h1>{$lang->edityouraccount}</h1>
{$notification_message}
<form id="changepassword_Form" name="changepassword_Form" method="post">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr><td colspan="2" class="subtitle">{$lang->changepassword}</td></tr>
        <tr>
            <td width="15%">{$lang->oldpassword}</td>
            <td width="85%"><input type="password" id="oldpassword" name="oldpassword" required="required"/></td>
        </tr>
        <tr>
            <td>{$lang->newpassword}</td>
            <td><input type="password" id="newpassword" name="newpassword" required="required"/></td>
        </tr>
        <tr>
            <td>{$lang->confirmpassword}</td>
            <td><input type="password" id="newpassword2" name="newpassword2" required="required" /></td>
        </tr>
        <tr><td colspan="2">
                <input type="submit" id="changepassword_Button" class="button main" value="{$lang->change}"/> <input type="reset" value="{$lang->reset}" class="button"/>
            </td>
        </tr>
        <tr>
            <td>
                <div id="changepassword_Results"></div>
            </td>
        </tr>
    </table>
</form>
<hr  />
<form id="modifyprofile_Form" name="modifyprofile_Form" method="post">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr><td colspan="3" class="subtitle">{$lang->editprofileinformation}</td></tr>
        <tr>
            <td><strong>{$lang->firstname}</strong></td><td><input type="text" id="firstName" name="firstName"  value="{$core->user[firstName]}" tabindex="1" required="required"/></td>
        </tr>
        <tr>
            <td><strong>{$lang->lastname}</strong></td><td><input type="text" id="lastName" name="lastName"  value="{$core->user[lastName]}" tabindex="3" required="required"/></td>
        </tr>
        <tr>
            <td><strong>{$lang->email}</strong></td>
            <td><input type="email" id="email" name="email" value="{$core->user[email]}" tabindex="4" required="required"/> <span id="email_Validation"></span></td>

        <tr><td colspan="3">
                <input type="submit" id="modifyprofile_Button"  value="{$lang->modify}" tabindex="17" class="button main"/> <input type="reset" value="{$lang->reset}" class="button"/>
                <div id="modifyprofile_Results"></div>
            </td></tr>
    </table>
</form>
<hr />
