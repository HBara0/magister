<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->createsurvey}</title>
        {$headerinc}
        <script language="javascript">
            $(function() {
                $(document).on('change', "input[name^='isExternal']", function() {
                    $("#internalinvitations_row, #externalinvitations_row, #externalinvitations_row2").toggle();
                    if($(this).val() == 0) {
                        $("input[id^='anonymousFilling'], input[id^='isPublicResults'], input[id^='isPublicFill']").attr("disabled", false);
                    }
                    else
                    {
                        $("input[id^='anonymousFilling'], input[id^='isPublicResults'], input[id^='isPublicFill']").attr("disabled", true);
                    }
                });
                $("select[id='stid']").trigger("change");
            });

        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->$action}</h1>
            <form  name="perform_surveys/{$action}_Form" id="perform_surveys/{$action}_Form" action="#" method="post">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="20%">{$lang->reference}</td>
                        <td width="80%"><input type="text" name="reference" id="reference"  tabindex="1" value="{$survey[reference]}" /></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">{$lang->subject}</td>
                        <td><input type="text" name="subject" id="subject"  tabindex="1" value="{$survey[subject]}" size="60"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->description}</td>
                        <td> <textarea cols="50" rows="5" name="description" class="txteditadv" id="description">{$survey[description]}</textarea></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">{$lang->category}</td>
                        <td>{$surveycategories_list}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">{$lang->template}</td>
                        <td>{$surveytemplates_list} <a href="index.php?module=surveys/createsurveytemplate" target="_blank"><img src="./images/addnew.png" alt="{$lang->add}"></a>
                            <a id="previewtemplate_link" href="" target="_blank"><img src="./images/icons/report.gif" border="0" title="{$lang->preview}" alt="{$lang->preview}"/></a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">{$lang->publicfill}</td>
                        <td>{$radiobuttons[isPublicFill]} <span class="smalltext">({$lang->publicfill_note})</span></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">{$lang->publicresult}</td>
                        <td>{$radiobuttons[isPublicResults]}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">{$lang->anonymousfilling}</td>
                        <td>{$radiobuttons[anonymousFilling]}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">{$lang->collectexternalresponses}</td>
                        <td>{$radiobuttons[isExternal]}</td>
                    </tr>
                    <tr>
                        <td>{$lang->closingdate}</td>
                        <td><input type="text" id="pickDate" name="closingDate" autocomplete="off" tabindex="1" value="{$survey[closingDate_output]}" title="{$lang->closingdate_tip}"/><input type="hidden" name="closingDate" id="altpickDate" value="{$survey[closingDate_value]}" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="thead">{$lang->surveysassociations} <a href="#associationssection" onClick="$('#associationssection').fadeToggle();">...</a></td>
                    </tr>
                    <tr>
                        <td colspan="2" id="associationssection" style="display:none;">
                            <table class="datatable" border="0" cellspacing="1" cellpadding="1">
                                <tr >
                                    <td>{$lang->employee}</td>
                                    <td>{$employees_list}</td>
                                    <td>{$lang->supplier}</td>
                                    <td>
                                        <input type='text'    id='supplier_1_autocomplete'  value="{$survey[associations][suppliername]}" autocomplete='off' size='40px'/>
                                        <input type='hidden' id='supplier_1_id' name='associations[spid]' value="{$survey[associations][spid]}" />
                                        <div id='searchQuickResults_supplier_1' class='searchQuickResults' style='display:none;'></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{$lang->affiliate}</td>
                                    <td>{$affiliates_list}</td>
                                    <td >{$lang->product}</td>
                                    <td>
                                        <input type='text' id='product_1_autocomplete' value="{$survey[associations][pid]}" autocomplete='off' size='40px'/>
                                        <input type='hidden' id='product_1_id' name='associations[pid]' value="{$survey[associations][pid]}" />
                                        <div id='searchQuickResults_product_1' class='searchQuickResults' style='display:none;'></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{$lang->segment}</td>
                                    <td>{$segments_list}</td>
                                    <td>{$lang->other}</td>
                                    <td><input name="associations[other]" type="text"></td>
                                </tr>
                                <tr>
                                    <td>{$lang->country}</td>
                                    <td>{$countries_list}</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="thead" colspan="2">{$lang->surveysinvitation}</td>
                    </tr>
                    <tr id="internalinvitations_row">
                        <td colspan="2">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tbody id="invitationsgroup_tbody">
                                    <tr id="1">
                                        <td colspan="2">
                                            <div style="width:100% ;height: 500px; overflow:auto; display:inline-block; vertical-align:top;">
                                                <table class="datatable" width="100%">
                                                    <tr class="altrow2">
                                                        <td colspan="3" style="font-style:italic;">{$lang->invitationgroup}</td>
                                                        <td colspan="2">
                                                            <div align="right" class="smalltext">{$lang->randomizeinvitations}
                                                                <input class="smalltext" accept="numeric"type="text" size="3" name="inviteesnumber[1]" id="inviteesnumber_1" value="0" title="{$lang->inviteesnumber_tip}">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr class="altrow2">
                                                        <th>&nbsp;</th>
                                                        <th>{$lang->invitee}</th>
                                                        <th>{$lang->affiliate}</th>
                                                        <th>{$lang->position}</th>
                                                        <th>{$lang->segment}</th>
                                                    </tr>
                                                    {$invitations_rows}
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr><td><img src="images/add.gif" id="addmore_invitationsgroup_invitations" alt="Add"></td></tr>
                                </tfoot>
                            </table>
                        </td>
                    </tr>
                    <tr id="externalinvitations_row" style="display:none">
                        <td>{$lang->externalinvitations}</td>
                        <td><textarea name="externalinvitations" cols="50" rows="5"></textarea></td>
                    </tr>
                    <tr id="externalinvitations_row2" style="display:none">
                        <td>{$lang->surveyheaderurl}</td>
                        <td><input style="width:40%" type="text" name="surveyHeader"></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="thead" style="margin-top:10px;">{$lang->customizeinvitationemail} <a href="#customizeinvitationmessage" onClick="$('#customizeinvitationmessage').fadeToggle();">...</a></td>
                    </tr>
                    <tr class="altrow2">
                        <td id="customizeinvitationmessage" style="display:none" colspan="2">
                            <div style="width:20%; display:inline-block; vertical-align:top;">{$lang->customizeinvitation}</div><div style="width:65%; display:inline-block; padding-bottom:5px; vertical-align:top;"><input type="checkbox" name="customInvitation" id="customInvitation"></div>
                            <div style="width:20%; display:inline-block; vertical-align:top;">{$lang->senderemail}</div><div style="width:65%; display:inline-block; padding-bottom:5px; vertical-align:top;"><input type="email" name="senderEmail" size="40"></div>
                            <div style="width:20%; display:inline-block; vertical-align:top;">{$lang->sendername}</div><div style="width:65%; display:inline-block; padding-bottom:5px; vertical-align:top;"><input type="text" name="senderName"  ></div>
                            <div style="width:20%; display:inline-block; vertical-align:top;">{$lang->invitationsubject}</div><div style="width:65%; display:inline-block; padding-bottom:5px; vertical-align:top;"><input type="text" name="customInvitationSubject" id="customInvitationSubject" size="60"></div>
                            <div style="width:20%; display:inline-block; vertical-align:top;">{$lang->invitationbody}</div>
                            <div style="width:65%; display:inline-block; vertical-align:top;">
                                <textarea name="customInvitationBody" id="customInvitationBody" class="txteditadv" cols="45" rows="10">{$defaultmsg}</textarea>
                                <div style="font-style:italic;">{$lang->custominvitationbody_note}</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr />
                            <input type="button" value="{$lang->$action}" id="perform_surveys/{$action}_Button" tabindex="26" class="button"/> <input type="reset" value="{$lang->reset}" class="button" />
                            <div id="perform_surveys/{$action}_Results"></div>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>