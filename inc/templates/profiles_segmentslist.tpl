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
            <table class="datatable_basic table table-bordered row-border hover order-column" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>{$lang->segment}
                        <th>{$lang->coordinators}</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>{$lang->segment}
                        <th>{$lang->coordinators}</th>
                    </tr>
                </tfoot>
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