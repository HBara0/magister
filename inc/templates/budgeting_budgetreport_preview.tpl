<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$report[title]}</title>
        {$headerinc}
        <link href="./css/report.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript">

        </script>
    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <form name="perform_budgeting/preview_Form" id="perform_budgeting/preview_Form" method="post" action="#">
                {$budgetreport_coverpage}
                {$budgeting_budgetrawreport}
                <input type="hidden" name="budgetid" value="{$budgetid}"/>
                <div align="right"><a href="index.php?module=budgeting/preview&action=exportexcel&bid={$budgetid}" target="_blank"><img src="././images/icons/xls.gif"/>{$lang->generateexcel}</a></div>
            </form>
        </td>
    </tr> 


    {$footer}
</body>
</html>