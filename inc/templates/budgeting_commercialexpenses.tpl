<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->commercialadminstrationexpenses}</title>
        {$headerinc}
        <script type="text/javascript">
            $(function() {
                $("input[id^='budgetexps']").live('keyup', function() {
                    var id = $(this).attr('id').split("_");
                    var yefPrevYear = parseFloat($('input[id=budgetexps_' + id[1] + '_' + id[2] + '_yefPrevYear]').val());
                    var budgetCurrent = parseFloat($('input[id=budgetexps_' + id[1] + '_' + id[2] + '_budgetCurrent]').val());

                    $('span[id=budgetexps_' + id[1] + '_' + id[2] + '_budYefPerc]').text((((budgetCurrent - yefPrevYear) / yefPrevYear) * 100).toFixed(2) + '%');
                    $('input[id=budgetexps_' + id[1] + '_' + id[2] + '_budYefPerc]').val((((budgetCurrent - yefPrevYear) / yefPrevYear) * 100).toFixed(2));

                    if(yefPrevYear == 0 || isNaN(((budgetCurrent - yefPrevYear) / yefPrevYear) * 100) == true) {
                        $('span[id=budgetexps_' + id[1] + '_' + id[2] + '_budYefPerc]').text('0' + '%');
                        $('input[id=budgetexps_' + id[1] + '_' + id[2] + '_budYefPerc]').val('0' + '%');
                    }

                    var category_subtotal = 0;
                    $('input[id$=' + id[2] + '_' + id[3] + '][id^=budgetexps]').each(function() {
                        category_subtotal += parseFloat(this.value);
                    });
                    if(category_subtotal > 0) {
                        $('div[id=subtotal_' + id[2] + '_' + id[3] + ']').text(category_subtotal);
                        $('input[id=subtotal_' + id[2] + '_' + id[3] + ']').val(category_subtotal);
                    }

                    var total = 0;
                    $('input[id^=subtotal_][id$=' + id[3] + ']').each(function() {
                        total += parseFloat(this.value);
                    });
                    if(total > 0) {
                        $('div[id=total_' + id[3] + ']').text(total);
                        $('input[id=total_' + id[3] + ']').val(total);
                        $('input[id^=finGenAdm_' + id[3] + ']').attr('max', total);
                    }

                    var totalyefPrevYear = parseFloat($('input[id=total_yefPrevYear]').val());
                    var totalbudgetCurrent = parseFloat($('input[id=total_budgetCurrent]').val());
                    if(!isNaN(((totalbudgetCurrent - totalyefPrevYear) / totalyefPrevYear) * 100)) {
                        $('div[id=total_budYefPerc]').text((((totalbudgetCurrent - totalyefPrevYear) / totalyefPrevYear) * 100).toFixed(2) + '%');
                    }

                    var subtotalyefPrevYear = parseFloat($('input[id=subtotal_' + id[2] + '_yefPrevYear]').val());
                    var subtotalbudgetCurrent = parseFloat($('input[id=subtotal_' + id[2] + '_budgetCurrent]').val());
                    if(!isNaN(((subtotalbudgetCurrent - subtotalyefPrevYear) / subtotalyefPrevYear) * 100)) {
                        $('div[id=subtotal_' + id[2] + '_budYefPerc]').text((((subtotalbudgetCurrent - subtotalyefPrevYear) / subtotalyefPrevYear) * 100).toFixed(2) + '%');
                    }

                    if($("input[id^='budgetexps']").val() != 0) {
                        $("input[id^='finGenAdm_']").trigger('keyup');
                    }
                });

                $("input[id^='finGenAdm_']").live('keyup', function() {
                    var financeid = $(this).attr('id').split("_");
                    if(($('input[id^=total_' + financeid[1] + ']').val().length) == 0){return;}
                    if(($('input[id=finGenAdm_' + financeid[1] + ']').val().length) == 0){return;}
                    $('div[id=comexpenses_' + financeid[1] + ']').text(parseFloat($('input[id=total_' + financeid[1] + ']').val()) - parseFloat($('input[id=finGenAdm_' + financeid[1] + ']').val()));
                    $('div[id=propfin_' + financeid[1] + ']').text(((parseFloat($('input[id=finGenAdm_' + financeid[1] + ']').val()) / parseFloat($('input[id=total_' + financeid[1] + ']').val())) * 100).toFixed(2) + '%');
                    $('div[id=propcomexpenses_' + financeid[1] + ']').text(((parseFloat($('div[id=comexpenses_' + financeid[1] + ']').text()) / parseFloat($('input[id=total_' + financeid[1] + ']').val())) * 100).toFixed(2) + '%');

                });
            });

        </script>
    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->commercialadminstrationexpenses}<br /><small>{$affiliate->name} - {$financialbudget_year}</small></h1>
            <form name="perform_budgeting/financialadminexpenses_Form" id="perform_budgeting/financialadminexpenses_Form"  action="#" method="post">
                <input type="hidden" id="identifier" name="identifier" value="{$sessionidentifier}">
                <table class="datatable" style="width:100%">
                    <tr class="thead">
                        <td style="width:50%">Company name:</td>
                        <td style="width:10%">{$lang->actual}</td>
                        <td style="width:10%">{$lang->budget}</td>
                        <td style="width:10%">{$lang->yef}</td>
                        <td style="width:10%">{$lang->budget}</td>
                        <td style="width:10%">% {$lang->budyef}</td>
                    </tr>
                    <tr style="width:100%">
                        <td style="width:50%"><input name="financialbudget[affid]" value="{$affid}" type="hidden"></td>
                        <td style="width:10%"><span>{$financialbudget_prev2year}</span></td>
                        <td style="width:10%"><span>{$financialbudget_prevyear}</span></td>
                        <td style="width:10%"><span>{$financialbudget_prevyear}</span></td>
                        <td style="width:10%"><span>{$financialbudget_year}</span><input name="financialbudget[year]" value="{$financialbudget_year}" type="hidden"></td>
                        <td style="width:10%"></td>
                    </tr>
                    {$budgeting_commercialexpenses_category}
                    <tr>
                        <td style="width:50%;font-weight:bold;">{$lang->totalexpenses}</td>
                        <td>
                            <div style="font-weight:bold;" id="total_actualPrevTwoYears">{$total[actualPrevTwoYears]}</div>
                            <input type="hidden" id="total_actualPrevTwoYears" value="{$total[actualPrevTwoYears]}">
                        </td>
                        <td>
                            <div style="font-weight:bold;" id="total_budgetPrevYear">{$total[budgetPrevYear]}</div>
                            <input type="hidden" id="total_budgetPrevYear" value="{$total[budgetPrevYear]}">
                        </td>
                        <td>
                            <div style="font-weight:bold;" id="total_yefPrevYear">{$total[yefPrevYear]}</div>
                            <input type="hidden" id="total_yefPrevYear" value="{$total[yefPrevYear]}">
                        </td>
                        <td>
                            <div style="font-weight:bold;" id="total_budgetCurrent">{$total[budgetCurrent]}</div>
                            <input type="hidden" id="total_budgetCurrent" value="{$total[budgetCurrent]}">
                        </td>
                        <td>
                            <div style="font-weight:bold;" id="total_budYefPerc"></div>
                            <input type="hidden" id="total_budYefPerc">
                        </td>
                    </tr>
                    {$budgeting_financeexpenses}
                    <hr />
                    <input type="submit" id="perform_budgeting/financialadminexpenses_Button" value="Proceed" class="button"/>
            </form>
            <div id="perform_budgeting/financialadminexpenses_Results"></div>
        </td>
    </tr>
    {$footer}
</body>
</html>