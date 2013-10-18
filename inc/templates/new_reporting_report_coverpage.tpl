<page>
    <div style='text-align: left; width: 100%;'>
        {$incomplete_report_notification}
        <div class="logo" style="height:40mm; width: 100%;">&nbsp;</div>
        <div style="vertical-align:bottom; text-align: center; height:40mm; width: 100%;"><h3>{$lang->quarterlymarketreport}</h3></div>
        <div style="text-align:center; vertical-align: top; font-size:18px; font-weight: bold; height:30mm; width: 100%;">{$report[quartername]}</div>
        <div style="text-align:center; font-size:22px; font-weight: bold; height:auto;">{$report[supplierlogo]}</div>
        <div style="font-style:italic; text-decoration:underline; height:10mm; width: 50%; position: relative; left: 30%;">{$lang->reportsenttofollowing}:</div>
        <div style="vertical-align:top; height:40mm; width: 50%; left: 30%; position: relative;">
            {$representatives_list}  
        </div>
        <div style="padding-top: 10mm; vertical-align:top; text-align: left; height:20mm; width: 70%; left: 10%; position: relative;">
             {$lang->contactchris}
        </div>
    </div>
</page>