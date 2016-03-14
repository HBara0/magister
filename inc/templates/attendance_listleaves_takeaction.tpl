<html>
    <head>
        <title>{$core->settings[systemtitle]}</title>
        {$headerinc}
        <script>
            $(function() {
                $('img[id^="replyto_"]').bind('click', function() {
                    var id = $(this).attr("id").split("_");
                    $("#inreplyto").val(id[1]);
                    var permission = id[2];
                    $('input[id$="_' + permission + '"]').prop('checked', true);
                    $('#message').focus();
                });
            });
        </script>
    </head>
    <body style="color:#ffffff;">
        <div align="center">
            {$preview_iteneraryframe}
            <table width="100%" border="0" cellpadding="0" cellspacing="0" id="errorbox">
                <tr>
                    <td class="content" style="color:#333333;">
                        <strong>{$leave[requester][displayName]}</strong><br />
                        {$lang->fromdate}: {$leave[fromDate_output]}<br />
                        {$lang->todate}: {$leave[toDate_output]}<br />
                        {$lang->leavetype}: {$leave[type_details][title]} {$leave[details_crumb]}<br />
                        {$lang->leavereason}: {$leave[reason]}
                        <hr />
                        <p><em>{$lang->sureapproveleavenote}</em></p>
                        <form action="index.php?module=attendance/listleaves" method="post">
                            <input type="hidden" name="action" value="perform_approveleave" />
                            <input type="hidden" id="toapprove" name="toapprove" value="{$core->input[requestKey]}" />
                            <input type="hidden" id="referrer" name="referrer" value="email" />
                            <input type='submit' value='{$lang->approveleave}' class='button'/>
                        </form>
                        <hr />
                        <p><em>{$lang->surerevokeleavenote}</em></p>
                        <form action="index.php?module=attendance/listleaves" method="post">
                            <input type="hidden" name="action" value="perform_revokeleave" />
                            <input type="hidden" id="torevoke" name="torevoke" value="{$core->input[id]}" />
                            <input type="hidden" id="referrer" name="referrer" value="email" />
                            <input type='submit' value='{$lang->revokeleave}' class='button'/>
                        </form>
                        <hr />
                        <div class='subtitle'>{$lang->conversation}</div>
                        <form name="perform_attendance/listleaves_Form" id="perform_attendance/listleaves_Form" action="#" method="post">
                            <input type="hidden" name="action" value="perform_sendmessage" />
                            <input type="hidden" value="" id="inreplyto" name="leavemessage[inReplyTo]"/>
                            <input type="hidden" id="messagerequestkey" name="messagerequestkey" value="{$core->input[requestKey]}" />
                            <input type="hidden" value="{$core->input[id]}" id="inreplyto" name="lid"/>
                            <div id="messagetoreply" style="display:block; padding: 8px;"><textarea id="message" cols="40" rows="5" name="leavemessage[message]" placeholder='{$lang->writeyourmsghere}'></textarea>
                                <div id="messagetoreply" style="display:none; padding:5px;">
                                    <span><input type="radio" id="permission_public" name="leavemessage[viewPermission]" title="{$lang->publictitle}" value="public" checked="checked">{$lang->public}</span>
                                    <span><input type="radio" disabled="disabled" id="permission_private" name="leavemessage[viewPermission]" title="{$lang->privatetitle}" value="private">{$lang->private}</span>
                                    <span><input type="radio" disabled="disabled" id="permission_limited" name="leavemessage[viewPermission]" title="{$lang->limitedtitle}" value="limited">{$lang->limited}</span>
                                </div>
                                <div><input type='submit' id="perform_attendance/listleaves_Button" value="&#x21b6; {$lang->reply}" class='button' /></div>
                                <div id="perform_attendance/listleaves_Results"></div>
                                <div style="display:block;">{$takeactionpage_conversation}</div>
                            </div>
                        </form>
                    </td>
                </tr>
                <tr><td class="footer">&nbsp;</td></tr>
            </table>

        </div>
    </body>
</html>