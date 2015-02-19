<h1>{$lang->reinitializebalances} - {$lang->select}</h1>
<form action="index.php?module=attendance/reinitializebalances&amp;action=reinitialize" method="post">
    <div style="width:15%; display:inline-block; margin: 5px; font-weight:bold;">{$lang->leavetype}</div>
    <div style="width:80%; display:inline-block; margin: 5px;">{$types_list}</div>
    <hr />
    <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p><strong>Important:</strong> This tool DELETES all existing balances and creates them from scratch. Kindly use it responsibly.<br />
            The Previous Balance column is very important in the case you had a system to manage the leaves balances before adopting this system. This column will specify what previous year balance to use in the first period which will be created for each selected user accordingly. This first period depends on the 1st leave available for the selected user(s) on the system.</p></div>

    <table width="100%" class="datatable">
        <tr><th>{$lang->employeename}</th><th>{$lang->balanceprevyear}</th></tr>
                {$tablerows}
    </table>
    <hr />
    <input type="submit" value="{$lang->next}" class="button">
</form>