<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->commercialadminstrationexpenses}</title>
        {$headerinc}

        <script>
            $(function() {
                $("input[id^='budgetforecastbs']").bind('keyup change ', function() {
                    var id = $(this).attr('id').split("_");
                    if($(this).val().length == 0) {
                        return;
                    }

                    var subtotal = total = 0;
                    $('input[id$=' + id[2] + '_' + id[3] + '][id^=budgetforecastbs]').each(function() {
                        if(!jQuery.isEmptyObject(this.value)) {
                            subtotal += parseFloat(this.value);
                        }
                    });
                    //budgetforecastbs,10,9,8
                    $('input[id^=subtotal_' + id[1] + '_' + id[2] + ']').val(subtotal);
                    $('input[id^=subtotal_][id$=_' + id[3] + ']').each(function() {
                        if(!jQuery.isEmptyObject(this.value)) {
                            total += parseFloat(this.value);
                        }
                    });

                    //  if(if(!jQuery.isEmptyObject())
                    $('div[id^=total_' + id[3] + ']').text(total);
                    $('input[id^=total_' + id[3] + ']').val(total);
                    // }

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