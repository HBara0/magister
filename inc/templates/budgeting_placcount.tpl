<script type="text/javascript">

    $(function() {
        var subtotal_income = subtotal_operatingprofit = subtotal_netincome = 0;
        $("input[id^='placcount']").bind('keyup change', function() {
            var id = $(this).attr('id').split("_");

            subtotal_income = $("div[id='total_income_" + id[2] + "']").text() - parseFloat($("input[id='total_sales_" + id[2] + "']").val());
            subtotal_netincome = $("div[id='total_netincome_" + id[2] + "']").text() - parseFloat($("div[id='total_operatingprofit_" + id[2] + "']").text());
            subtotal_operatingprofit = $("div[id='total_operatingprofit_" + id[2] + "']").text() - parseFloat($("div[id='total_income_" + id[2] + "']").text()) - parseFloat($("div[id='total_admcomexpenses_" + id[2] + "']").text());

            var v = 0;
            $("input[id^='" + id[0] + "_" + id[1] + "_" + id[2] + "']").each(function() {
                if(!jQuery.isEmptyObject(this.value)) {
                    v += parseFloat(this.value);
                }
            });
            eval("subtotal_" + id[1] + "=" + v);
            if(subtotal_income !== 0) {
                $("div[id='total_income_" + id[2] + "']").text((subtotal_income + parseFloat($("input[id='total_sales_" + id[2] + "']").val())).toFixed(2));
                ;
            }

            $("div[id='total_operatingprofit_" + id[2] + "']").text((subtotal_operatingprofit + parseFloat($("div[id='total_income_" + id[2] + "']").text()) + parseFloat($("div[id='total_admcomexpenses_" + id[2] + "']").text())).toFixed(2));
            $("div[id='total_netincome_" + id[2] + "']").text((subtotal_netincome + parseFloat($("div[id='total_operatingprofit_" + id[2] + "']").text())).toFixed(2));
            $("input[id='total_netincome_" + id[2] + "']").val((subtotal_netincome + parseFloat($("div[id='total_operatingprofit_" + id[2] + "']").text())).toFixed(2));

            var yefPrevYear = $("input[id^='" + id[0] + "_" + id[1] + "_yefPrevYear_" + id[3] + "']").val();
            var actualPrevTwoYears = $("input[id^='" + id[0] + "_" + id[1] + "_actualPrevTwoYears_" + id[3] + "']").val();
            var budgetPrevYear = $("input[id^='" + id[0] + "_" + id[1] + "_budgetPrevYear_" + id[3] + "']").val();
            var budgetCurrent = $("input[id^='" + id[0] + "_" + id[1] + "_budgetCurrent_" + id[3] + "']").val();

            $("div[id='" + id[0] + "_" + id[1] + "_yefactual_" + id[3] + "']").text('0.00 %');
            $("div[id='" + id[0] + "_" + id[1] + "_yefbud_" + id[3] + "']").text('0.00 %');
            $("div[id='" + id[0] + "_" + id[1] + "_budyef_" + id[3] + "']").text('0.00 %');
            if(actualPrevTwoYears != 0) {
                $("div[id='" + id[0] + "_" + id[1] + "_yefactual_" + id[3] + "']").text(((((yefPrevYear - actualPrevTwoYears) / actualPrevTwoYears) * 100).toFixed(2)) + ' %');
            }
            if(budgetPrevYear != 0) {
                $("div[id='" + id[0] + "_" + id[1] + "_yefbud_" + id[3] + "']").text(((((yefPrevYear - budgetPrevYear) / budgetPrevYear) * 100).toFixed(2)) + ' %');
            }
            if(yefPrevYear != 0) {
                $("div[id='" + id[0] + "_" + id[1] + "_budyef_" + id[3] + "']").text(((((budgetCurrent - yefPrevYear) / yefPrevYear) * 100).toFixed(2)) + ' %');
            }

        });
    });

</script>
<h1>{$lang->profitandlossaccount}<br /><small>{$affiliate->name} - {$budget_data['year']}</small></h1>
    {$output_currency}
<form name="perform_budgeting/profitlossaccount_Form" id="perform_budgeting/profitlossaccount_Form"  action="#" method="post">
    <input type="hidden" id="identifier" name="identifier" value="{$sessionidentifier}">
    <table class="datatable" style="width:100%;table-layout:fixed;">
        {$budgeting_header}
        {$output}
    </table>
    <hr />
    <input type="{$type}" id="perform_budgeting/profitlossaccount_Button" value="Save" class="button"/>
</form>
<div id="perform_budgeting/profitlossaccount_Results"></div>
