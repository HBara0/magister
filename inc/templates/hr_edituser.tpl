<h1>{$lang->modifyemployeeinformation} ($user[employeeNum])</h1>
<form action="#" method="post" id="perform_hr/edituser_Form" name="perform_hr/edituser_Form">
    <input type="hidden" id="uid" name="uid" value="{$user[uid]}">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td><strong>{$lang->firstname}</strong></td><td><input type="text" id="firstName" name="firstName" value="{$user[firstName]}" required="required" tabindex="7"/> {$lang->middlename} <input type="text" id="middleName" name="middleName" value="{$user[middleName]}" tabindex="8"/> <strong>{$lang->lastname}</strong> <input type="text" id="lastName" required="required" name="lastName" value="{$user[lastName]}" tabindex="9"/></td>
        </tr>
        <tr>
            <td>{$lang->gender}</td><td><select id="gender" name="gender"><option value="0">&nbsp;</option><option value="1"{$selectedoptions[gender][1]}>{$lang->female}</option><option value="2"{$selectedoptions[gender][2]}>{$lang->male}</option></select></td>
        </tr>
        <tr>
            <td>{$lang->dateofbirth}</td><td><input type="text" id="pickDate_dateofbirth" autocomplete="off" tabindex="2" value="{$user[birthDate_output]}" /><input type="hidden" name="birthDate" id="altpickDate_dateofbirth" value="{$user[birthDate_formatted]}"/> {$lang->placeofbirth} <input type="text" value="{$user[birthPlace]}" name="birthPlace" id="birthPlace"></td>
        </tr>
        <tr>
            <td>{$lang->nationality}</td><td>{$nationality_list}</td>
        </tr>
        <tr>
            <td>{$lang->religiousviews}</td><td>{$religiousviews_list}</td>
        </tr>
        <tr>
            <td>{$lang->maritalstatus}</td><td><select id="maritalStatus" name="maritalStatus"><option value="0"{$selectedoptions[maritalStatus][0]}>{$lang->single}</option><option value="1"{$selectedoptions[maritalStatus][1]}>{$lang->married}</option><option value="2"{$selectedoptions[maritalStatus][2]}>{$lang->engaged}</option><option value="3"{$selectedoptions[maritalStatus][3]}>{$lang->widowed}</option><option value="4"{$selectedoptions[maritalStatus][4]}>{$lang->divorced}</option></select> <input type="checkbox" name="hasChildren" id="hasChildren" value="1"{$checkedboxes[hasChildren]}> {$lang->haschildren}</td>
        </tr>
        <tr>
            <td valign="top">{$lang->passportinfo}</td><td><textarea cols="50" rows="5" name="passportInfo" id="passportInfo">{$user[passportInfo]}</textarea></td>
        </tr>
        <tr>
            <td colspan="2"><br /><hr /></td>
        </tr>
        <tr><td colspan="2" class="subtitle">{$lang->contactinfo}</td></tr>
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
            <td>{$lang->pobox}</td>
            <td><input type="text" id="poBox" name="poBox" accept="numeric" value="{$user[poBox]}" tabindex="23"/></td>
        </tr>
        <tr>
            <td>{$lang->telephone}</td>
            <td><input type="text" id="telephone_intcode" name="telephone_intcode" size="3" maxlength="3" accept="numeric" value="{$phones[telephone][intcode]}" /> <input type="text" id="telephone_areacode" name="telephone_areacode" size='4' maxlength="4" accept="numeric" value="{$phones[telephone][areacode]}" /> <input type="text" id="telephone_number" name="telephone_number" accept="numeric" value="{$phones[telephone][number]}" /><br />
                <input type="text" id="telephone2_intcode" name="telephone2_intcode" size="3" maxlength="3" accept="numeric" value="{$phones[telephone2][intcode]}" /> <input type="text" id="telephone2_areacode" name="telephone2_areacode" size='4' maxlength="4" accept="numeric" value="{$phones[telephone2][areacode]}" /> <input tpe="text" id="telephone2_number" name="telephone2_number" accept="numeric" value="{$phones[telephone2][number]}" />
            </td>
        </tr>
        <tr>
            <td>{$lang->mobile}</td>
            <td><input type="text" id="mobile_intcode" name="mobile_intcode" size="3" maxlength="3" accept="numeric" value="{$phones[mobile][intcode]}" /> <input type="text" id="mobile_areacode" name="mobile_areacode" size='4' maxlength="4" accept="numeric" value="{$phones[mobile][areacode]}" /> <input type="text" id="mobile_number" name="mobile_number" accept="numeric" value="{$phones[mobile][number]}" /><br />
                <input type="text" id="mobile2_intcode" name="mobile2_intcode" size="3" maxlength="3" accept="numeric" value="{$phones[mobile2][intcode]}" /> <input type="text" id="mobile2_areacode" name="mobile2_areacode" size='4' maxlength="4" accept="numeric" value="{$phones[mobile2][areacode]}" /> <input type="text" id="mobile2_number" name="mobile2_number" accept="numeric" value="{$phones[mobile2][number]}" />
            </td>
        </tr>
        <tr>
            <td>{$lang->bbpin}</td>
            <td><input type="text" id="bbPin" name="bbPin" value="{$user[bbPin]}"/></td>
        </tr>
        <tr>
            <td colspan="2"><br /><hr /></td>
        </tr>
        <tr>
            <td colspan="2" class="subtitle">{$lang->employementinfo}</td>
        </tr>
        <tr>
            <td>{$lang->employementclassification}</td><td><select id="empClassification" name="empClassification"><option value="1"{$selectedoptions[empClassification][1]}>{$lang->fulltime}</option><option value="2"{$selectedoptions[empClassification][2]}>{$lang->parttime}</option><option value="3"{$selectedoptions[empClassification][3]}>{$lang->casual}</option></select></td>
        </tr>
        <tr>
            <td>{$lang->position}</td><td>{$positions_list}</td>
        </tr>
        <tr>
            <td>{$lang->reportsto}</td>
            <td><input type='text' id='user_1_autocomplete' value="{$user[reportsToName]}"/><input type="text" size="3" id="user_1_id_output" value="{$user[reportsTo]}" disabled/><input type='hidden' id='user_1_id' name='reportsTo' value="{$user[reportsTo]}" /><div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div></td>
        </tr>
        <tr>
            <td>{$lang->assistant}</td>
            <td><input type='text' id='user_2_autocomplete' value="{$user[assistantName]}"/><input type="text" size="3" id="user_2_id_output" value="{$user[assistant]}" disabled/><input type='hidden' id='user_2_id' name='assistant' value="{$user[assistant]}" /><div id='searchQuickResults_2' class='searchQuickResults' style='display:none;'></div></td>
        </tr>
        <tr>
            <td>{$lang->segments}</td><td>{$segments_list}</td>
        </tr>
        <tr>
            <td valign="top">{$lang->jobdescription}</td><td><textarea cols="60" rows="10" id="jobDescription" name="jobDescription" tabindex="25">{$user[jobDescription]}</textarea></td>
        </tr>
        <tr>
            <td>{$lang->joindate}</td><td><input type="text" id="pickDate_joinDate" autocomplete="off" value="{$user[joinDate_output]}" /><input type="hidden" name="joinDate" id="altpickDate_joinDate" value="{$user[joinDate_formatted]}"/></td>
        </tr>
        <tr>
            <td>{$lang->firstjobdate}</td><td><input type="text" id="pickDate_firstJobDate" autocomplete="off" value="{$user[firstJobDate_output]}" /><input type="hidden" name="firstJobDate" id="altpickDate_firstJobDate" value="{$user[firstJobDate_formatted]}" /> <a href="#" title="{$lang->firstjobdate_tip}"><img src="./images/icons/question.gif" border="0"/></a></td>
        </tr>
        <tr>
            <td>{$lang->leavedate}</td><td><input type="text" id="pickDate_leaveDate" autocomplete="off" value="{$user[leaveDate_output]}" /><input type="hidden" name="leaveDate" id="altpickDate_leaveDate" value="{$user[leaveDate]}"/></td>
        </tr>
        <tr>
            <td>{$lang->noticeperiod}</td><td><input type="text" value="{$user[noticePeriod]}" id="noticePeriod" name="noticePeriod" accept="numeric"></td>
        </tr>
        <tr>
            <td colspan="2"><br /><hr /></td>
        </tr>
        <tr><td colspan="2" class="subtitle">{$lang->paymentinformation}</td></tr>
        <tr>
            <td>{$lang->salary}</td>
            <td><input type="text" id="salary" name="salary" accept="numeric" value="{$user[salary]}"/></td>
        </tr>
        <tr>
            <td>{$lang->paymentmethod}</td><td><select id="paymentMethod" name="paymentMethod"><option value="1"{$selectedoptions[paymentMethod][1]}>{$lang->banktransfer}</option><option value="2"{$selectedoptions[paymentMethod][2]}>{$lang->cash}</option><option value="3"{$selectedoptions[paymentMethod][3]}>{$lang->check}</option></select></td>
        </tr>
        <tr>
            <td>{$lang->bankname}</td><td><input name="bankName" id="bankName" value="{$user[bankName]}"> {$lang->bankbranch} <input name="bankBranch" id="bankBranch" value="{$user[bankBranch]}"></td>
        </tr>
        <tr>
            <td>{$lang->accountnumber}</td><td><input name="bankAccountNumber" id="bankAccountNumber" value="{$user[bankAccountNumber]}"></td>
        </tr>
        <tr>
            <td>{$lang->iban}</td><td><input name="iban" id="iban" value="{$user[iban]}"></td>
        </tr>
        <tr>
            <td>{$lang->taxinformation}</td><td><input name="taxInfo" id="taxInfo" value="{$user[taxInfo]}"></td>
        </tr>
        <tr>
            <td>{$lang->socialsecuritynumber}</td><td><input name="socialSecurityNumber" id="socialSecurityNumber" value="{$user[socialSecurityNumber]}"></td>
        </tr>
        <tr>
            <td colspan="2"><br /><hr /></td>
        </tr>
        <tr>
            <td colspan="2" class="subtitle">{$lang->experienceinformation}</td>
        </tr>
        <tr>
            <td colspan="2">
                <table width="100%">
                    <tbody id="experience_tbody">
                        {$experience_rows}
                    </tbody>
                    <tr><td colspan="2"><img src="images/add.gif" id="addmore_experience" alt="{$lang->add}"></td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /><hr /></td>
        </tr>
        <tr>
            <td colspan="2" class="subtitle">{$lang->educationinformation}</td>
        </tr>
        <tr>
            <td colspan="2">
                <table width="100%">
                    <tbody id="education_tbody">
                        {$education_rows}
                    </tbody>
                    <tr><td colspan="2"><img src="images/add.gif" id="addmore_education" alt="{$lang->add}"></td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /><hr /></td>
        </tr>
        <tr>
            <td colspan="2" class="subtitle">{$lang->managementcomments}</td>
        </tr>
        <tr>
            <td colspan="2"><textarea cols="60" rows="10" id="managementComments" name="managementComments" >{$user[managementComments]}</textarea></td>
        </tr>
        <tr>
            <td colspan="2" align="left">
                <input type="submit" value="{$lang->savecaps}" id="perform_hr/edituser_Button"/> <input type="reset" value="{$lang->reset}" />
                <div id="perform_hr/edituser_Results"></div>
            </td>
        </tr>
    </table>
</form>
