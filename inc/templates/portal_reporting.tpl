<div class="portalbox">
    <div class="portalbox_header">
        <a href="index.php?module=reporting/home">{$lang->reporting}</a>
    </div>
    <div>
        {$lang->currqreportingstats}:
        <ul>
            <li>{$countall_current_quarterly} {$lang->intotal}</li>
            <li>{$countall_current_quarterly_unfinalized} {$lang->unfinalized}</li>
        </ul>
        <hr />
        {$lang->duereports}:
        <ul>
            {$due_reports_list}
        </ul>
        <hr />
        {$lang->lastfinalized}:
        <ul>
            {$last_reports_list} 
        </ul>
    </div>
</div>