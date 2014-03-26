<page>
    <div style="page-break-before:always;"></div>
    <div class="paperContainer">
        <a name="qr-{$report[affid]}-{$report[spid]}"></a>
        {$highlightbox}
        {$reporting_report_newoverviewbox[segments][amount]}
        {$reporting_report_newoverviewbox[products][amount]}

        {$reporting_report_newoverviewbox[segments][purchasedQty]}
        {$reporting_report_newoverviewbox[products][purchasedQty]}
        {$reporting_report_newoverviewbox[products][soldQty]}

        {$keycustomersbox}
        {$marketreportbox}
        <div style="text-align:left; font-style:italic;" class="smalltext">{$marketreport[authors_output]}</div> 
        <br />
        <table class="reporttable"  style="width: 100%;">
            {$contributors}
        </table>


    </div>
</page>