<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->requirementslist}</title>
        {$headerinc}
    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->requirementslist}</h1>
            {$requirements_list}
        </td>
    </tr>
    {$footer}
</body>
</html>