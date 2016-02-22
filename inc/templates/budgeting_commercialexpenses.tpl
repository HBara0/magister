<div class="container">
    <script type="text/javascript">
        $(function() {
            $("input[id^='budgetexps']").bind('keyup change', function() {
                var id = $(this).attr('id').split("_");
                var yefPrevYear = parseFloat($('input[id=budgetexps_' + id[1] + '_' + id[2] + '_yefPrevYear]').val());
                var budgetCurrent = parseFloat($('input[id=budgetexps_' + id[1] + '_' + id[2] + '_budgetCurrent]').val());

                $('span[id=budgetexps_' + id[1] + '_' + id[2] + '_budYefPerc]').text((((budgetCurrent - yefPrevYear) / yefPrevYear) * 100).toFixed(2) + '%');
                $('input[id=budgetexps_' + id[1] + '_' + id[2] + '_budYefPerc]').val((((budgetCurrent - yefPrevYear) / yefPrevYear) * 100).toFixed(2));

                if(yefPrevYear == 0 || isNaN(((budgetCurrent - yefPrevYear) / yefPrevYear) * 100) == true) {
                    $('span[id=budgetexps_' + id[1] + '_' + id[2] + '_budYefPerc]').text('0.00' + '%');
                    $('input[id=budgetexps_' + id[1] + '_' + id[2] + '_budYefPerc]').val('0.00');
                }

                var category_subtotal = 0;
                $('input[id$=' + id[2] + '_' + id[3] + '][id^=budgetexps]').each(function() {
                    if(!jQuery.isEmptyObject(this.value)) {
                        category_subtotal += parseFloat(this.value);
                    }
                });
                $('div[id=subtotal_' + id[2] + '_' + id[3] + ']').text(category_subtotal);
                $('input[id=subtotal_' + id[2] + '_' + id[3] + ']').val(category_subtotal);


                var total = 0;
                $('input[id^=subtotal_][id$=' + id[3] + ']').each(function() {
                    if(!jQuery.isEmptyObject(this.value)) {
                        total += parseFloat(this.value);
                    }
                });

                $('div[id=total_' + id[3] + ']').text(total);
                $('input[id=total_' + id[3] + ']').val(total);
                $('input[id^=finGenAdm_' + id[3] + ']').attr('max', total);
                $('input[id=finGenAdm_max' + id[3] + ']').val(total);


                var totalyefPrevYear = parseFloat($('input[id=total_yefPrevYear]').val());
                var totalbudgetCurrent = parseFloat($('input[id=total_budgetCurrent]').val());
                var perc = (((totalbudgetCurrent - totalyefPrevYear) / totalyefPrevYear) * 100).toFixed(2);
                if(!jQuery.isNumeric(perc)) {
                    perc = 0;
                }
                $('div[id=total_budYefPerc]').text(perc + '%');
                var subtotalyefPrevYear = parseFloat($('input[id=subtotal_' + id[2] + '_yefPrevYear]').val());
                var subtotalbudgetCurrent = parseFloat($('input[id=subtotal_' + id[2] + '_budgetCurrent]').val());
                $('div[id=subtotal_' + id[2] + '_budYefPerc]').text('0.00%')
                if(!isNaN(((subtotalbudgetCurrent - subtotalyefPrevYear) / subtotalyefPrevYear) * 100)) {
                    if(subtotalyefPrevYear != 0) {
                        $('div[id=subtotal_' + id[2] + '_budYefPerc]').text((((subtotalbudgetCurrent - subtotalyefPrevYear) / subtotalyefPrevYear) * 100).toFixed(2) + '%');
                    }
                }
                $("input[id^='finGenAdm_']").trigger('keyup');
            });

            $("input[id^='finGenAdm_']").bind('keyup change', function() {
                var financeid = $(this).attr('id').split("_");
                if(($('input[id^=total_' + financeid[1] + ']').val()) == 0) {
                    $('input[id=finGenAdm_' + financeid[1] + ']').val(0);
                    $('div[id=comexpenses_' + financeid[1] + ']').text(0);
                    $('div[id=propfin_' + financeid[1] + ']').text('0.00%');
                    $('div[id=propcomexpenses_' + financeid[1] + ']').text('0.00%');
                    return;
                }
                if(($('input[id=finGenAdm_' + financeid[1] + ']').val().length) == 0) {
                    return;
                }
                $('div[id=comexpenses_' + financeid[1] + ']').text((parseFloat($('input[id=total_' + financeid[1] + ']').val()) - parseFloat($('input[id=finGenAdm_' + financeid[1] + ']').val())).toFixed(2));
                $('div[id=propfin_' + financeid[1] + ']').text(((parseFloat($('input[id=finGenAdm_' + financeid[1] + ']').val()) / parseFloat($('input[id=total_' + financeid[1] + ']').val())) * 100).toFixed(2) + '%');
                $('div[id=propcomexpenses_' + financeid[1] + ']').text(((parseFloat($('div[id=comexpenses_' + financeid[1] + ']').text()) / parseFloat($('input[id=total_' + financeid[1] + ']').val())) * 100).toFixed(2) + '%');
            });
        });

    </script>
    <h1>{$lang->commercialadminstrationexpenses}<br /><small>{$affiliate->name} - {$financialbudget_year}</small></h1>
        {$output_currency}
    <form name="perform_budgeting/financialadminexpenses_Form" id="perform_budgeting/financialadminexpenses_Form"  action="#" method="post">
        <input type="hidden" id="identifier" name="identifier" value="{$sessionidentifier}">
        <table class="datatable" style="width:100%;table-layout:fixed;">
            {$budgeting_header}
            {$output}
            <hr />
        </table>
        <input type="{$type}" id="perform_budgeting/financialadminexpenses_Button" value="Save" class="button"/>
    </form>
    <div id="perform_budgeting/financialadminexpenses_Results"></div>
</div>