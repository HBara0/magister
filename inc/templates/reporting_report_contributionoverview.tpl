<page>
    <table style="width:100%;">
        <tr>
            <td class="logo" style="width:100%;">&nbsp;</td>
        </tr>
        <tr>
            <td style="width:100%; text-align:left;">
                <table class="reportbox" style="width: 100%;">
                    <tr><td colspan="2" class="cathead">{$lang->reportcontributorsoverview}</td></tr>
                    <tr><td colspan="2" class="cathead" style="color:#FFFFFF; padding-bottom:2px;">{$lang->auditedby}: {$auditor[employeeName]} (<a href="mailto:{$auditor[email]}" style="color:#FFFFFF;">{$auditor[email]}</a>)</td></tr>
                    {$contributors_overview_entries}
                </table>
            </td>
        </tr>
    </table>
</page>