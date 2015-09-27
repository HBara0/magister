<hr />
<div class="subtitle">{$lang->invitations}</div>
<div style="width: 100%; overflow:auto; display: inline-block; vertical-align: top;">

    <form action="#" method="post" id="perform_surveys/createsurvey_Form" name="perform_surveys/createsurvey_Form">
        <input type="hidden" value="{$core->input[identifier]}" name="identifier">
        <input name="action" value="sendinvitations" type="hidden" />
        <div><input value="{$lang->send} {$lang->invitations}" type="button" id="perform_surveys/createsurvey_Button" class="button"/></div>


        <table class="datatable">
            <tbody>
                <tr id="internalinvitations_row" {$display[internalinvitations]}>
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
                                                {$invitations_row}

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
                <tr id="externalinvitations_row" {$display[externalinvitations]}>
                    <td>{$lang->externalinvitations}</td>
                    <td><textarea name="externalinvitations" cols="70" rows="5"></textarea></td>
                </tr>

            </tbody>
        </table>
    </form>
    <div id="perform_surveys/createsurvey_Results" ></div>

</div>