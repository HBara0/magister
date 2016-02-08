<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$pagetitle}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$pagetitle}</h1>
            <form action="#" method="post" id="perform_users/{$actiontype}_Form" name="perform_users/{$actiontype}_Form">
                {$uidfield}
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="20%"><strong>{$lang->username}</strong></td><td><input type="text" id="username" name="username" value="{$user[username]}" tabindex="1"/></td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->password}</strong></td><td><input type="password" id="password" name="password" title="{$lang->passwordpattern_tooltip}" pattern="(?=^.{8,}$)(?=.*\d)(?=.*\W+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" tabindex="2"/></td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->confirmpassword}</strong></td><td><input type="password" id="password2" name="password2" title="{$lang->passwordpattern_tooltip}" pattern="(?=^.{8,}$)(?=.*\d)(?=.*\W+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" tabindex="3"/></td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->email}</strong></td><td><input type="text" id="email" name="email" value="{$user[email]}" tabindex="4"/> <span id="email_Validation"></span></td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->usergroup}</strong></td><td>{$usergroups_list}</td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->addusergroups}</strong></td><td>{$addusergroups_list}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="width:100%; margin-top:10px; margin-bottom:15px; height: 200px; overflow:auto; display:inline-block; vertical-align:top;">
                                <table width="100%"  class="datatable" border="0" cellspacing="2" cellpadding="1">
                                    <tr class='thead'><th>{$lang->affiliates}</th><th>{$lang->mainaffiliate}</th><th>{$lang->otheraffiliates}</th><th>{$lang->hr}</th><th>{$lang->audit}</th><th>&nbsp;</th></tr>
                                            {$affiliates_list}
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->reportsto}</strong></td>
                        <td><input type='text' id='user_1_autocomplete' value="{$user[reportsToName]}"/><input type="text" size="3" id="user_1_id_output" value="{$user[reportsTo]}" disabled/><input type='hidden' id='user_1_id' name='reportsTo' value="{$user[reportsTo]}" /><div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div></td>
                    </tr>
                    <tr>
                        <td>{$lang->assistant}</td>
                        <td><input type='text' id='user_2_autocomplete' value="{$user[assistantName]}"/><input type="text" size="3" id="user_2_id_output" value="{$user[assistant]}" disabled/><input type='hidden' id='user_2_id' name='assistant' value="{$user[assistant]}" /><div id='searchQuickResults_2' class='searchQuickResults' style='display:none;'></div></td>
                    </tr>
                    <tr><td colspan="2"><hr /></td></tr>
                    <tr>
                        <td><strong>{$lang->firstname}</strong></td><td><input type="text" id="firstName" name="firstName" value="{$user[firstName]}" tabindex="7"/> {$lang->middlename} <input type="text" id="middleName" name="middleName" value="{$user[middleName]}" tabindex="8"/> <strong>{$lang->lastname}</strong> <input type="text" id="lastName" name="lastName" value="{$user[lastName]}" tabindex="9"/></td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->displayname}</strong></td><td><input type="text" id="displayName" name="displayName" value="{$user[displayName]}" tabindex="10"/></td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->country}</strong></td><td>{$countries_list}</td>
                    </tr>
                    <tr>
                        <td>{$lang->city}</td>
                        <td><input type="text" id="city" name="city" value="{$user[city]}" tabindex="11"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->address}</td>
                        <td><input type="text" id="addressLine1" name="addressLine1" value="{$user[addressLine1]}" tabindex="12"/><br /><input type="text" id="addressLine2" name="addressLine2" value="{$user[addressLine2]}" tabindex="13"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->buildingname}</td><td><input type="text" id="building" name="building" value="{$user[building]}" tabindex="14"/></td>

                    </tr>
                    <tr>
                        <td>{$lang->postcode}</td><td><input type="text" id="postCode" name="postCode" accept="numeric" value="{$user[postCode]}" tabindex="15"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->telephone}</td>
                        <td><input type="text" id="telephone_intcode" name="telephone_intcode" size="3" maxlength="3" accept="numeric" value="{$telephone[1][intcode]}" tabindex="17"/> <input type="text" id="telephone_areacode" name="telephone_areacode" size='4' maxlength="4" accept="numeric" value="{$telephone[1][areacode]}" tabindex="18"/> <input type="text" id="telephone_number" name="telephone_number" accept="numeric" value="{$telephone[1][number]}" tabindex="19" /><br />
                            <input type="text" id="telephone2_intcode" name="telephone2_intcode" size="3" maxlength="3" accept="numeric" value="{$telephone[2][intcode]}" tabindex="20" /> <input type="text" id="telephone2_areacode" name="telephone2_areacode" size='4' maxlength="4" accept="numeric" value="{$telephone[2][areacode]}" tabindex="21" /> <input type="text" id="telephone2_number" name="telephone2_number" accept="numeric" value="{$telephone[2][number]}" tabindex="22" />
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->pobox}</td>
                        <td><input type="text" id="poBox" name="poBox" accept="numeric" value="{$user[poBox]}" tabindex="23"/></td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr /></td>
                    </tr>
                    <tr>
                        <td>{$lang->position}</td><td>{$positions_list}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="width:100% ;height: 200px; overflow:auto; display:inline-block; vertical-align:top;">
                                <table class="datatable" width="100%">
                                    <thead><tr><th class="thead" colspan=3>{$lang->segments}</th></tr></thead>
                                                {$segments_list}
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-weight:bold;"><hr />{$lang->assignsupplier}:</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table width="100%">
                                <thead>
                                <th style="width: 50%; text-align: left;">{$lang->supplier}</th><th style="text-align: left;">{$lang->affiliates}</th><th style="width: 10%; text-align: left;">{$lang->isvalidator}</th>
                                </thead>
                                <tbody id="suppliers_tbody">
                                    {$suppliers_rows}
                                </tbody>
                                <tr><td colspan="3"><img src="../images/add.gif" id="addmore_suppliers" alt="{$lang->add}"></td><tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="font-weight:bold;"><hr />{$lang->assigncustomers}:</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table class="datatable_basic table table-bordered row-border hover order-column" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="customerfilter_checkall"></th>
                                        <th>{$lang->customername}</th>
                                        <th>{$lang->affiliate}</th>
                                        <th>{$lang->segment}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th><input type="checkbox" id="customerfilter_checkall"></th>
                                        <th>{$lang->customername}</th>
                                        <th>{$lang->affiliate}</th>
                                        <th>{$lang->segment}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    {$customer_list}
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="left">
                            <input type="button" value="{$lang->$actiontype}" id="perform_users/{$actiontype}_Button"  tabindex="26"/> <input type="reset" value="{$lang->reset}" />
                            <div id="perform_users/{$actiontype}_Results"></div>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>