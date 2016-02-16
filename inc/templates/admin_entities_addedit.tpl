<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$pagetitle}</title>
        {$headerinc}
        <script language="javascript" type="text/javascript">
            $(function() {
                if($("select[id='type']").val() == 's' || $(this).val() == 'cs') {
                    $("tr[id='supplierType'], #parentcompany, #coveredcountries_section").show();
                    $("#parentcustomer").hide();
                }
                else {
                    $("tr[id='supplierType'], #parentcompany, #coveredcountries_section").hide();
                    $("#parentcustomer").show();
                }

                $("select[id='type']").change(function() {
                    if($(this).val() == 's' || $(this).val() == 'cs') {
                        $("#createReports,#noQReportReq,#noQReportSend").removeAttr("disabled");
                        $("tr[id='supplierType'], tr[id='parentcompany'], #coveredcountries_section").show();
                        $("tr[id='parentcustomer']").hide();
                    }
                    else {
                        $("#createReports,#noQReportReq,#noQReportSend").attr("disabled", "true");
                        $("tr[id='supplierType'], tr[id='parentcompany'], #coveredcountries_section").hide();
                        $("tr[id='parentcustomer']").show();
                    }
                });

                $("#noQReportReq").change(function() {
                    if($(this).is(":checked")) {
                        $("#createReports,#noQReportSend").attr("disabled", "true");
                    }
                    else
                    {
                        $("#createReports,#noQReportSend").removeAttr("disabled");
                    }
                });
            });</script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$pagetitle}</h1>
            <form action="#" method="post" id="perform_entities/{$actiontype}_Form" name="perform_entities/{$actiontype}_Form" enctype="multipart/form-data">
                {$eidfield}
                <table width="100%" border="0" cellpadding="0" cellspacing="2">
                    <tr>
                        <td width="10%"><strong>{$lang->entitytype}</strong></td><td>{$types_list} </td>
                    </tr>
                    <tr id="supplierType">
                        <td ><strong>{$lang->supptype}</strong></td><td >{$supptypes_list} </td>
                    </tr>
                    <tr>
                        <td ><strong>{$lang->presence}</strong></td><td >{$presence_list} </td>
                    </tr>
                    <tr>
                        <td width="20%" valign="top"><strong>{$lang->companyname}</strong></td><td><input type="text" id="companyName" name="companyName" value="{$entity[companyName]}"/> {$lang->abbreviation} <input type="text" id="companyNameAbbr" name="companyNameAbbr" value="{$entity[companyNameAbbr]}" size="10"/> <input type="hidden" id="logo" name="logo" value="{$entity[logo]}"> <a id="showpopup_setentitylogo" class="showpopup"><img src="../images/icons/photo.gif" border="0" alt="Add Logo"></a></td>
                    </tr>
                    <tr>
                        <td width="20%" valign="top"><strong>{$lang->companyshortname}</strong></td><td><input type="text" id="companyNameShort" name="companyNameShort" value="{$entity[companyNameShort]}"/><div id="entitylogo_placeholder">{$entity[logo_output]}</div></td>
                    </tr>
                    <tr id="parentcompany">
                        <td width="20%" valign="top">{$lang->parentcompany}</td><td><input type='text' id='supplier_1_autocomplete' autocomplete="off" value="{$entity[parent_companyName]}"/><input type="text" size="3" id="supplier_1_id_output" value="{$entity[parent]}" disabled/><input type='hidden' id='supplier_1_id' name='parent' value="{$entity[parent]}" /><div id='searchQuickResults_supplier_1' class='searchQuickResults' style='display:none;'></div></td>
                    </tr>
                    <tr id="parentcustomer">
                        <td valign="top">{$lang->parentcompany}</td><td><input type='text' id='customer_1_autocomplete' value="{$entity[parent_companyName]}"/><input type="hidden" size="3" id="customer_1_id_output" value="{$entity[parent]}" disabled/><input type='hidden' id='customer_1_id' name='parent' value="{$entity[parent]}" /><div id='searchQuickResults_customer_1' class='searchQuickResults' style='display:none;'></div></td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->segments}</strong></td><td>{$segments_list}</td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->affiliate}</strong></td><td>{$affiliates_list}</td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->country}</strong></td><td>{$countries_list}</td>
                    </tr>
                    <tr>
                        <td>{$lang->city}</td>
                        <td><input type="text" id="city" name="city" value="{$entity[city]}" /></td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->address}*</strong></td>
                        <td><input type="text" id="addressLine1" name="addressLine1" value="{$entity[addressLine1]}" /><br /><input type="text" id="addressLine2" name="addressLine2" value="{$entity[addressLine2]}" /></td>
                    </tr>
                    <tr>
                        <td>{$lang->buildingname}</td><td><input type="text" id="building" name="building" value="{$entity[building]}" /> <input type="text" id="floor" name="floor" size='3' maxlength="3" value="{$entity[floor]}" /></td>
                    </tr>
                    <tr>
                        <td>{$lang->postcode}</td><td><input type="text" id="postCode" name="postCode" value="{$entity[postCode]}" accept="numeric" /></td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->telephone}*</strong></td>
                        <td><input type="text" id="telephone_intcode" name="telephone_intcode" size="3" maxlength="3" value="{$entity[phone1][intcode]}" accept="numeric" /> <input type="text" id="phone_areacode" name="telephone_areacode" size='4' maxlength="4" value="{$entity[phone1][areacode]}" accept="numeric" /> <input type="text" id="telephone_number" name="telephone_number" value="{$entity[phone1][number]}" accept="numeric" /><br />
                            <input type="text" id="telephone2_intcode" name="telephone2_intcode" size="3" maxlength="3" value="{$entity[phone2][intcode]}" accept="numeric" /> <input type="text" id="phone2_areacode" name="telephone2_areacode" size='4' maxlength="4" value="{$entity[phone2][areacode]}" accept="numeric"  /> <input type="text" id="telephone2_number" name="telephone2_number" value="{$entity[phone2][number]}" accept="numeric" />
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->fax}</td>
                        <td><input type="text" id="fax_intcode" name="fax_intcode" size="3" maxlength="3" value="{$entity[fax1][intcode]}" accept="numeric" /> <input type="text" id="fax_areacode" name="fax_areacode" size='4' maxlength="4" value="{$entity[fax1][areacode]}" accept="numeric" /> <input type="text" id="fax_number" name="fax_number" value="{$entity[fax1][number]}" accept="numeric" /><br />
                            <input type="text" id="fax2_intcode" name="fax2_intcode" size="3" maxlength="3" value="{$entity[fax2][intcode]}" accept="numeric" /> <input type="text" id="fax2_areacode" name="fax2_areacode" size='4' maxlength="4" value="{$entity[fax2][areacode]}" accept="numeric"  /> <input type="text" id="fax2_number" name="fax2_number" value="{$entity[fax2][number]}" accept="numeric" />
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->pobox}</td>
                        <td><input type="text" id="poBox" name="poBox" value="{$entity[poBox]}" accept="numeric" /></td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->email}*</strong></td>
                        <td><input type="text" id="email" name="mainEmail" value="{$entity[mainEmail]}" /> <span id="email_Validation"></span></td>
                    </tr>
                    <tr>
                        <td>{$lang->website}</td>
                        <td><input type="text" id="website" name="website" value="{$entity[website]}" /></td>
                    </tr>
                    <tr><td colspan="2"><hr /></td><tr>
                    <tr>
                        <td colspan="2" class="subtitle">{$lang->assignedemployee}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table width="100%">
                                <thead>
                                <th style="width: 50%; text-align: left;">{$lang->username}</th><th style="text-align: left;">{$lang->affiliates}</th><th style="width: 10%; text-align: left;">{$lang->isvalidator}</th>
                                </thead>
                                <tbody id="users_tbody">
                                    {$users_rows}
                                </tbody>
                                <tr><td colspan="3"><img src="../images/add.gif" id="addmore_users" alt="{$lang->add}"><input type="hidden" name="users_numrows" id="numrows" value="{$users_counter}"></td><tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="subtitle">{$lang->representatives}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table width="100%">
                                <tbody id="representatives_tbody">
                                    {$representative_rows}
                                </tbody>
                                <tr><td><img src="../images/add.gif" id="addmore_representatives" alt="{$lang->add}"><input type="hidden" name="rep_numrows" id="numrows" value="{$rep_counter}"></td><tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="2"><hr /></td>
                    <tr>
                        <td colspan="2" align="left"><input name="noQReportReq" id="noQReportReq" type="checkbox" value="1"{$checkedboxes[noQReportReq]}{$noqreportreq_disabled}> {$lang->noqreportsrequired}</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="left"><input name="noQReportSend" id="noQReportSend" type="checkbox" value="1"{$checkedboxes[noQReportSend]}{$noqreportsend_disabled}> {$lang->noqreportstosend}</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="left"><input name="createReports" id="createReports" type="checkbox" value="1"{$createreports_disabled}> {$lang->alsocreatecurrentreports}</td>
                    </tr>
                    <tr><td colspan="2"><hr /></td></tr>
                            {$contractinfo_section}
                    <tr>
                        <td colspan="2" align="left">
                            <input type="button" value="{$lang->$actiontype}" id="perform_entities/{$actiontype}_Button" /> <input type="reset" value="{$lang->reset}"/>
                            <div id="perform_entities/{$actiontype}_Results"></div>
                        </td>
                    </tr>
                </table>
            </form>
            <div id='popup_setentitylogo' title="{$lang->setentitylogo}">
                <iframe id='uploadFrame' name='uploadFrame' src='#' style="display:none; margin:0px;"></iframe>
                <form action="index.php?module=entities/add&amp;action=do_uploadlogo" method="post" enctype="multipart/form-data" target="uploadFrame">
                    {$lang->selectfile}: <input type="file" id="uploadfile" name="uploadfile"><br />
                    <div style="font-style:italic; margin: 5px;">{$lang->onlyfiletypesallowed}</div>
                    <input type="submit" class='button' value="{$lang->savecaps}" onClick="$('#upload_Result').show();">
                </form>
                <div id="upload_Result" style="display:none;"><img src="{$core->settings[rootdir]}/images/loading.gif" /> {$lang->uploadinprogress}</div>
            </div>
        </td>
    </tr>
    {$footer}
</body>
</html>