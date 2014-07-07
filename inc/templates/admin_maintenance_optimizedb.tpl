<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->optimizedatabase}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->optimizedatabase}</h1>
            <p>{$lang->selectoptimizetables}:</p>
            <form id="perform_maintenance/optimizedb_Form" name="perform_maintenance/optimizedb_Form" action="#" method="post">
                {$tables_list}
                <p><input type="button" id="perform_maintenance/optimizedb_Button" value="{$lang->optimize}" /> <input type="reset" value="{$lang->reset}" /></p>
            </form>
            <div id="perform_maintenance/optimizedb_Results"></div>
        </td>
    </tr>
    {$footer}
</body>
</html>