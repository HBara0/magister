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
            <td><input type="password" id="newpassword" name="newpassword" required="required" title="{$lang->passwordpattern_tooltip}" pattern="(?=^.{8,}$)(?=.*\d)(?=.*\W+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$"/></td>
        </tr>
        <tr>
            <td>{$lang->confirmpassword}</td>
            <td><input type="password" id="newpassword2" name="newpassword2" required="required" title="{$lang->passwordpattern_tooltip}" pattern="(?=^.{8,}$)(?=.*\d)(?=.*\W+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$"/></td>
        </tr>
        <tr><td colspan="2">
                <input type="submit" id="changepassword_Button" class="button main" value="{$lang->change}"/> <input type="reset" value="{$lang->reset}" class="button"/>
                <div id="changepassword_Results"></div>
            </td></tr>
    </table>
</form>
<hr  />
<div align="right"><a href="users.php?action=profile&uid={$core->user[uid]}">{$lang->viewyourprofile}</a></div>
<form id="modifyprofile_Form" name="modifyprofile_Form" method="post">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr><td colspan="3" class="subtitle">{$lang->editprofileinformation}</td></tr>
        <tr>
            <td rowspan="15" align="center" valign="top" class="border_right"><a id="showpopup_changeprofilepic" class="showpopup"><img id="profilePicture" src="{$core->settings[rootdir]}/{$core->settings[profilepicdir]}/{$core->user[profilePicture]}" alt="{$lang->changeprofilepic}" border="0" style="cursor:pointer;"/></a><hr /></td>
            <td><strong>{$lang->firstname}</strong></td><td><input type="text" id="firstName" name="firstName"  value="{$core->user[firstName]}" tabindex="1" required="required"/></td>
        </tr>
        <tr>
            <td>{$lang->middlename}</td><td><input type="text" id="middleName" name="middleName"  value="{$core->user[middleName]}" tabindex="2"/></td>
        </tr>
        <tr>
            <td><strong>{$lang->lastname}</strong></td><td><input type="text" id="lastName" name="lastName"  value="{$core->user[lastName]}" tabindex="3" required="required"/></td>
        </tr>
        <tr>
            <td><strong>{$lang->email}</strong></td>
            <td><input type="email" id="email" name="email" value="{$core->user[email]}" tabindex="4" required="required"/> <span id="email_Validation"></span></td>
        </tr>
        <td><strong>{$lang->skype}</strong></td>
        <td><input type="text" id="skype" name="skype" value="{$core->user[skype]}" tabindex="4" required="required"/></td>
        </tr>
        <tr>
            <td>{$lang->city}</td>
            <td><input type="text" id="city" name="city"  value="{$core->user[city]}" tabindex="5"/></td>
        </tr>
        <tr>
            <td>{$lang->address}</td>
            <td><input type="text" id="addressLine1" name="addressLine1"  value="{$core->user[addressLine1]}" tabindex="6"/><br /><input type="text" id="addressLine2" name="addressLine2"  value="{$core->user[addressLine2]}" tabindex="7"/></td>
        </tr>
        <tr>
            <td>{$lang->buildingname}</td>
            <td><input type="text" id="building" name="building"  value="{$core->user[building]}" tabindex="8"/></td>
        </tr>
        <tr>
            <td>{$lang->postcode}</td>
            <td><input type="text" id="postCode" name="postCode" accept="numeric"  value="{$core->user[postCode]}" tabindex="9"/></td>
        </tr>
        <tr>
            <td>{$lang->pobox}</td>
            <td><input type="text" id="poBox" name="poBox" accept="numeric" value="{$core->user[poBox]}" tabindex="16"/></td>
        </tr>
        <tr>
            <td>{$lang->internalextension}</td>
            <td><input size="4" maxlength="4" name="internalExtension" id="internalExtension" value="{$core->user[internalExtension]}"/></td>
        </tr>
        <tr>
            <td>{$lang->telephone}</td>
            <td>+ <input type="text" id="telephone_intcode" name="telephone_intcode" size="3" maxlength="3" accept="numeric"  value="{$phones[telephone][intcode]}" tabindex="10"/> <input type="text" id="telephone_areacode" name="telephone_areacode" size='4' maxlength="4" accept="numeric"  value="{$phones[telephone][areacode]}" tabindex="11"/> <input type="text" id="telephone_number" name="telephone_number" accept="numeric"  value="{$phones[telephone][number]}" tabindex="12"/> &times; <input size="4" maxlength="4" name="telephoneExtension" id="telephoneExtension" value="{$core->user[telephoneExtension]}"/><br />
                + <input type="text" id="telephone2_intcode" name="telephone2_intcode" size="3" maxlength="3" accept="numeric"  value="{$phones[telephone2][intcode]}" tabindex="13"/> <input type="text" id="telephone2_areacode" name="telephone2_areacode" size='4' maxlength="4" accept="numeric"  value="{$phones[telephone2][areacode]}" tabindex="14"/> <input type="text" id="telephone2_number" name="telephone2_number" accept="numeric"  value="{$phones[telephone2][number]}" tabindex="15"/>  &times; <input size="4" maxlength="4" name="telephone2Extension" id="telephone2Extension" value="{$core->user[telephone2Extension]}"/> </td>
        </tr>
        <tr>
            <td>{$lang->mobile}</td>
            <td>
                + <input type="text" id="mobile_intcode" name="mobile_intcode" size="3" maxlength="3" accept="numeric" value="{$phones[mobile][intcode]}" /> <input type="text" id="mobile_areacode" name="mobile_areacode" size='4' maxlength="4" accept="numeric" value="{$phones[mobile][areacode]}" /> <input type="text" id="mobile_number" name="mobile_number" accept="numeric" value="{$phones[mobile][number]}" /> <input type='checkbox' name='mobileIsPrivate' id='mobileIsPrivate' value='1' title="{$lang->mobileisprivate_tip}"{$checkedboxes[mobileIsPrivate]} /> {$lang->private}<br />
                + <input type="text" id="mobile2_intcode" name="mobile2_intcode" size="3" maxlength="3" accept="numeric" value="{$phones[mobile2][intcode]}" /> <input type="text" id="mobile2_areacode" name="mobile2_areacode" size='4' maxlength="4" accept="numeric" value="{$phones[mobile2][areacode]}" /> <input type="text" id="mobile2_number" name="mobile2_number" accept="numeric" value="{$phones[mobile2][number]}" /> <input type='checkbox' name='mobile2IsPrivate' id='mobile2IsPrivate' value='1' title="{$lang->mobileisprivate_tip}"{$checkedboxes[mobile2IsPrivate]} /> {$lang->private}
            </td>
        </tr>
        <tr>
            <td>{$lang->bbpin}</td>
            <td><input type="text" id="bbPin" name="bbPin" value="{$core->user[bbPin]}"/></td>
        </tr>
        <tr>
            <td>{$lang->birthdayisprivate}</td>
            <td>
                <input type='checkbox' name='birthdayIsPrivate' id='birthdayIsPrivate' value='1' title="{$lang->birthdayisprivate_tip}"{$checkedboxes[birthdayIsPrivate]} />
            </td>
        </tr>

        <tr><td>{$download_card_button}</td>
        </tr>
        <tr><td colspan="3" class="subtitle"><br />{$lang->accountsettings}</td></tr>
        <tr>
            <td colspan="2">{$lang->defaultmodule}</td>
            <td>{$moduleslist}</td>
        </tr>
        <tr>
            <td colspan="2">{$lang->defaultlanguage}</td>
            <td>{$languageslist}</td>
        </tr>
        <!--<tr>
            <td colspan="2">{$lang->timezone}</td>
            <td>{$timezoneslist}</td>
        </tr>-->
        <tr>
            <td colspan="2">{$lang->newfilenotification}</td>
            <td><input type='checkbox' name='newFilesNotification' id='newFilesNotification' value='1'{$checkedboxes[newFilesNotification]}></td>
        </tr>
        <tr><td colspan="3" class="subtitle">&nbsp;</td></tr>
        <tr><td colspan="3">
                <input type="submit" id="modifyprofile_Button"  value="{$lang->modify}" tabindex="17" class="button main"/> <input type="reset" value="{$lang->reset}" class="button"/>
                <div id="modifyprofile_Results"></div>
            </td></tr>
    </table>
</form>
{$editprofilepage_profilepicform}
<div id="popup_getsignature" title="{$lang->emailsignature}">
    <div class="subtitle">{$lang->imagesignature}</div>
    <img src="users.php?action=profile&amp;action=generatesignature" border="0" alt="{$lang->emailsignature}">
    <div class="subtitle" style="margin-top: 10px;">{$lang->imagesignaturecompact}</div>
    <img src="users.php?action=profile&amp;action=generatesignaturemin" border="0" alt="{$lang->emailsignature}">
    <div class="subtitle" style="margin-top: 10px;">{$lang->plaintextsignature}</div>
    <p>
        {$signature[text]}
    </p>
    <br />
    <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px; text-align:center; font-weight:bold;"><a href="users.php?action=profile&amp;action=downloadsignature" title="{$lang->downloadsignature}">{$lang->downloadsignature}</a></div>
</div>
<hr />
<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; padding-top: 25px; margin-bottom:10px; text-align:center; font-weight:bold; height: 50px; font-size:14px; position: relative;"><a id="showpopup_getsignature" class="showpopup" href="#popup_getsignature">{$lang->generatesignature}</a><div style='position: absolute; top:1px; right: 1px;'>{$helplinks['how-to-generate-signature']}</div></div>
