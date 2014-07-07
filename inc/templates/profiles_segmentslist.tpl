<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->segmentslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->segmentslist}</h1>
            <table class="datatable" style="display:{$datatable_display};">
                <thead>
                    <tr>
                        <th style="width:50%;">{$lang->segment}
                        <th style="width:50%;">{$lang->coordinators}</th>
                    </tr>
                </thead>
                <tbody>
                    {$segments_rows}
                </tbody>
            </table>
        </div>
    </td>
</tr>
{$footer}
</body>
</html>