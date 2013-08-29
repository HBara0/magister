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
                <input type="hidden" name="budgetid" value="{$bid}">
            </form>
            {$budgetreport_coverpage}
            {$budgeting_budgetrawreport}
            <div align="right">{$tools}</div>
            <span><a href="#tableofcontent" class="scrollup" title="{$lang->clicktoscroll}"></a></span>
        </td>
    </tr>
    {$footer}
</body>
</html>