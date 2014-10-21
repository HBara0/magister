<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->commercialadminstrationexpenses}</title>
        {$headerinc}

        <script>
            $(function() {

                $("input[id^='budgetforecastbs_']").bind('change keyup', function() {
                    var id = $(this).attr('id').split("_");
                    var total = 0;//headcount_actualPrevTwoYears
                    $('input[id$=' + id[3] + '_' + id[4] + ']').each(function() {
                        if(!jQuery.isEmptyObject(this.value)) {
                            total += parseInt(this.value);
                        }

                    });
                    $('div[id^=total_' + id[3] + ']').text(total);
                    $('input[id^=total_' + id[3] + ']').val(total);

                });



            });

        </script>
    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->budgetforecast} <small>{$affiliate->name} - {$financialbudget_year}</small></h1>
            {$output_currency}
            <form name="perform_budgeting/forecastbalancesheet_Form" id="perform_budgeting/forecastbalancesheet_Form"  action="#" method="post">

                <input name="financialbudget[affid]" value="{$affid}" type="hidden">
                <input name="financialbudget[year]" value="{$financialbudget_year}" type="hidden">
                {$accountitems_output}
                {$total}
                <br/>

                <input type="submit" id="perform_budgeting/forecastbalancesheet_Button" value="{$lang->savecaps}" class="button"/>
            </form>
            <div id="perform_budgeting/forecastbalancesheet_Results"></div>
        </body>
    </td>
</tr>
{$footer}
</html>