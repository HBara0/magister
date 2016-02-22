<div class="container">
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
                $('span[id^=total_' + id[3] + ']').text(total);
                $('span[id^=gtotal_][id*=' + id[3] + ']').text(total);
                $('input[id^=total_' + id[3] + ']').val(total);
            });
        });
    </script>
    <h1>{$lang->budgetforecast} <small>{$affiliate->name} {$financialbudget_year}</small></h1>
    {$output_currency}
    <form name="perform_budgeting/forecastbalancesheet_Form" id="perform_budgeting/forecastbalancesheet_Form"  action="#" method="post">

        <input name="financialbudget[affid]" value="{$affid}" type="hidden">
        <input name="financialbudget[year]" value="{$financialbudget_year}" type="hidden">
        {$accountitems_output}
        {$total}
        <br/>
        <hr />
        <input type="submit" id="perform_budgeting/forecastbalancesheet_Button" value="{$lang->savecaps}" class="button"/>
    </form>
    <div id="perform_budgeting/forecastbalancesheet_Results"></div>
</body>
</div>