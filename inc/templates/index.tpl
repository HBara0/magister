<script src="/js/fillreport.js" type="text/javascript"></script>

<h1>{$lang->reportsoverview}</h1>
<div style="width:45%; float:left">
    <ul>
        {$admin_overview}
        <li>{$lang->overviewcurrentquarter}</li>
        <li> <em>{$lang->overviewall}</em></li>
    </ul>
</div>

<div style="width:45%; float:right">
    <strong>{$lang->duexdays}</strong>
    <ul>
        {$due_reports_list}
    </ul>

    <strong>{$lang->lastfinalized}</strong>
    <ul>
        {$last_reports_list}
    </ul>
</div>
