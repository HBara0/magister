<html>
    <head>
        <title>{$core->settings[systemtitle]}</title>
        {$headerinc}
    </head>
    <body style="color:#ffffff;">
        <div align="center">
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
                    </td>
                </tr>
                <tr><td class="footer">&nbsp;</td></tr>
            </table>
        </div>
    </body>
</html>