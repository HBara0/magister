<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->generatedstatreport}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->generatedstatreport}</h3>
            <span class="subtitle">Report about {$mainsubject}</span>
            <ul type="square">
                <li>{$lang->numberreportsbetweendates}</li>
                <li>{$lang->maxvisistperday}</li>
            </ul>

            <strong>{$lang->visitslist}</strong>
            <table class="datatable">
                <tr>
                    <th>{$lang->calltype}</th>
                    <th>{$lang->dateofvisit}</th>
                        {$table_header_contd}
                    <th>&nbsp;</th>
                </tr>
                {$visits_list}
            </table>

            <p>{$piechart}</p>
        </td>
    </tr>
    {$footer}
</body>
</html>