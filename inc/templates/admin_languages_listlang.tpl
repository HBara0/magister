<h1>{$lang->listlangfiles}</h1>
<table width="100%" class="datatable">
    <thead>
        <tr>
            <th width="42%">{$lang->filename}</th>
            <th width="25%">{$lang->timecreated}</th>
            <th width="25%">{$lang->timemodified}</th>
            <th width="7%"><a href="index.php?module=languages/manage&amp;type=add" title="{$lang->addlanguagefile}" target="_blank"><img src="../images/add.gif" border="0" alt="{$lang->addlanguagefile}"> {$lang->add}</a></th>
        </tr>
    </thead>
    <tbody>
        {$listlang_rows}
    </tbody>
</table>