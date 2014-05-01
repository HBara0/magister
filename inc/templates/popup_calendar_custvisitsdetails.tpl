<div id="popup_custvisitdetails" title="{$lang->customervisit}">
    <strong>{$visit[fromDate_output]} - {$visit[toDate_output]}</strong><br />
    <span style="font-weight:bold;">{$visit[customername_output]}</span>
    {$visit[employeename]}
    <span style="font-style:italic;">{$visit[type]} / {$visit[purpose]}</span>
    <div style='position:absolute; bottom:5px; left: 5px;'>{$control_icons}</div>
</div>