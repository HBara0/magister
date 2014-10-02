<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->commercialadminstrationexpenses}</title>
        {$headerinc}
        <script type="text/javascript">
        </script>

    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->commercialadminstrationexpenses}</h1>
            <form name="perform_budgeting/investmentfollowup_Form" id="perform_budgeting/investmentfollowup_Form"  action="#" method="post">
                <input type="hidden" id="identifier" name="identifier" value="{$sessionidentifier}">
                <table>

                    {$budgeting_header}
                    {$budgeting_investexpenses_categories}

                </table
                <br/>

                <input type="submit" id="perform_budgeting/investmentfollowup_Button" value="{$lang->savecaps}" class="button"/>
            </form>
            <div id="perform_budgeting/investmentfollowup_Results"></div>
        </body>
    </td>
</tr>
{$footer}
</html>