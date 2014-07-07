<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->phpinfo}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->phpinfo}</h1>
            {$php_info}
        </td>
    </tr>
    {$footer}
</body>
</html>