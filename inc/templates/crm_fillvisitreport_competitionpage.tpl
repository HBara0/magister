<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillvisitreport}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->fillvisitreport} - {$lang->competitiondetails}</h1>
            <form action="index.php?module=crm/previewvisitreport&referrer=fill" method="post">
                <input type="hidden" name="identifier" value="{$identifier}">
                <div class='subtitle'>{$lang->commentsoncompetition}</div>
                {$competition_fields}
                <div align="center"><input type="button" value="{$lang->prev}" class="button" onclick="goToURL('index.php?module=crm/fillvisitreport&stage=visitdetails&identifier={$identifier}')"> <input type="submit" value="{$lang->next}" class="button"> </div>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>