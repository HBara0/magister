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
            <h1>{$lang->commercialadminstrationexpenses}</h1>
            <form name="perform_budgeting/commercialexpenses_Form" id="perform_budgeting/commercialexpenses_Form"  action="#" method="post">
                <input type="hidden" id="identifier" name="identifier" value="{$sessionidentifier}">
                {$budgeting_commercialexpenses_categories}
                <br/>
                {$budgeting_financeexpenses}
                <input type="submit" id="perform_budgeting/commercialexpenses_Button" value="proceed" class="button"/>
            </form>
            <div id="perform_budgeting/commercialexpenses_Results"></div>
        </body>
    </td>
</tr>
{$footer}
</html>