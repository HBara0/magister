<page>
    <table style="width:100%;">
        <tr>
            <td class="logo" style="width:100%;">&nbsp;</td>
        </tr>
        <tr>
            <td style="width:100%; text-align:left;">
                <table class="reportbox" style="width: 100%;">
                    <tr><td colspan="2" class="cathead">{$lang->reportsummary}</td></tr>
                    <tr><td colspan="2" class="cathead" style="color:#FFFFFF; padding-top:10px; padding-bottom:5px;">{$lang->auditedby}: {$auditor[employeeName]} (<a href="mailto:{$auditor[email]}" style="color:#FFFFFF;">{$auditor[email]}</a>)</td></tr>
                    <tr><td colspan="2" class="thead" style="color:#000000;">
                            {$report_summary[summary]}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</page>