<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$pagetitle}</title>

        {$headerinc}
        <script src="{$core->settings[rootdir]}/js/profiles_marketintelligence.js" type="text/javascript"></script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$pagetitle}</h1>
            {$isfinalized}
            {$visitreportspages}
            <p><hr /></p>
        <p>{$prev_visitreports_list}</p>
        <div align="right">{$tools}</div>
    </td>
</tr>
{$footer}
</body>
</html>