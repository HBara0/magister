<div class="container">
    <script type="text/javascript">
        $(function() {
            $("input[id^='budgetinvst_']").bind('change keyup', function() {
                var id = $(this).attr('id').split("_");

                var category_subtotal = total = 0;
                $('input[id$=' + id[2] + '_' + id[3] + '][id^=budgetinvst]').each(function() {
                    if(!jQuery.isEmptyObject(this.value)) {
                        category_subtotal += parseFloat(this.value);
                    }
                });
                $('div[id=subtotal_' + id[2] + '_' + id[3] + ']').text(category_subtotal.toFixed(2));
                $('input[id=subtotal_' + id[2] + '_' + id[3] + ']').val(category_subtotal.toFixed(2));

                $('input[id^=subtotal_][id$=' + id[3] + ']').each(function() {
                    if(!jQuery.isEmptyObject(this.value)) {
                        total += parseFloat(this.value);
                    }
                });
                $('div[id=total_' + id[3] + ']').text(total.toFixed(2));

                if($('input[id^=budgetinvst_' + id[1] + '_' + id[2] + '_budgetPrevYear]').val().length === 0 || $('input[id^=budgetinvst_' + id[1] + '_' + id[2] + '_yefPrevYear]').val().length === 0) {
                    return;
                }
                $('div[id=budgetinvst_' + id[1] + '_' + id[2] + '_percVariation]').text((((parseFloat($('input[id=budgetinvst_' + id[1] + '_' + id[2] + '_yefPrevYear]').val()) - parseFloat($('input[id=budgetinvst_' + id[1] + '_' + id[2] + '_budgetPrevYear]').val())) / parseFloat($('input[id=budgetinvst_' + id[1] + '_' + id[2] + '_budgetPrevYear]').val())) * 100).toFixed(2) + '%');
                $('input[id=budgetinvst_' + id[1] + '_' + id[2] + '_percVariation]').val((((parseFloat($('input[id=budgetinvst_' + id[1] + '_' + id[2] + '_yefPrevYear]').val()) - parseFloat($('input[id=budgetinvst_' + id[1] + '_' + id[2] + '_budgetPrevYear]').val())) / parseFloat($('input[id=budgetinvst_' + id[1] + '_' + id[2] + '_budgetPrevYear]').val())) * 100).toFixed(2) + '%');

                $('div[id=subtotal_' + id[2] + '_percVariation]').text((((parseFloat($('input[id=subtotal_' + id[2] + '_yefPrevYear]').val()) - parseFloat($('input[id=subtotal_' + id[2] + '_budgetPrevYear]').val())) / parseFloat($('input[id=subtotal_' + id[2] + '_budgetPrevYear]').val())) * 100).toFixed(2) + '%');
                $('div[id=total_percVariation]').text((((parseFloat($('div[id=total_yefPrevYear]').text()) - parseFloat($('div[id=total_budgetPrevYear]').text())) / parseFloat($('div[id=total_budgetPrevYear]').text())) * 100).toFixed(2) + '%');
            });
        });
    </script>

    <h1>{$lang->investmentfollowup}<br /><small>{$affiliate->name} {$financialbudget_year}</small></h1>
        {$output_currency}
    <form name="perform_budgeting/investmentfollowup_Form" id="perform_budgeting/investmentfollowup_Form"  action="#" method="post">
        <input type="hidden" id="identifier" name="identifier" value="{$sessionidentifier}">
        <table style="width:100%;table-layout:fixed;">
            {$budgeting_header}
            {$budgeting_investexpenses_categories}
        </table
        <br/>
        <input type="{$type}" id="perform_budgeting/investmentfollowup_Button" value="{$lang->savecaps}" class="button"/>
    </form>
    <div id="perform_budgeting/investmentfollowup_Results"></div>
</body>
</div>