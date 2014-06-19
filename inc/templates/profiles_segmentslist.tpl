<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->segmentlist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->segmentlist}</h3>
            <table class="datatable" style="display:{$datatable_display};">
                <thead>
                    <tr>
                        <th style="width:50%;">{$lang->segment}

                        <th style="width:50%;">{$lang->coordinator}</th>

                    </tr>
                </thead>
                <tbody>
                    {$segment_list}
                </tbody>

            </table>
        </div>
    </td>
</tr>
{$footer}
</body>

</html>