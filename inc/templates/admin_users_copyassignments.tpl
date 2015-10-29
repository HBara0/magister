<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->copyassignments}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->copyassignments}</h1>
            <form action="#" method="post" id="perform_users/copyassignments_Form" name="perform_users/copyassignments_Form">
                From User: {$fromuser_selectlist}<br />
                To User: {$touser_selectlist}<br />
                Affiliate: {$affiliate_selectlist}<br />
                Segments: {$segments_selectlist}<br />
                Assignments <input type="checkbox" name="transfer[assignment]" value="1">&nbsp;&nbsp;
                User Transfer Assignments <input type="checkbox" name="transfer[userassignments]" value="1"><br />
                Customers <input type="checkbox" name="types[]" value="c">&nbsp;&nbsp;
                Suppliers <input type="checkbox" name="types[]" value="s"><br />

                <input type="button" value="{$lang->savecaps}" id="perform_users/copyassignments_Button" tabindex="26"/>
                <div id="perform_users/copyassignments_Results"></div>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>