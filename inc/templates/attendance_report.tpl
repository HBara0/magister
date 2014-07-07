<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->attendancereport}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->attendancereport}</h1>
            <div align="center">
                {$attendance_report}
            </div>
            <div align="right">{$tools}</div>
        </td>
    </tr>
    {$footer}
</body>
</html>