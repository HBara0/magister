<script type="text/javascript">
    $(function() {
        $("input[id^='headcount_']").bind('change keyup', function() {
            var id = $(this).attr('id').split("_");
            var total = 0;
            $('input[id^=headcount_' + id[1] + ']').each(function() {
                if(!jQuery.isEmptyObject(this.value)) {
                    total += parseInt(this.value);
                }
            });
            $('div[id=total_' + id[1] + ']').text(total);
        });
    });
</script>
<h1>{$lang->headcount}<br /><small>{$affiliate->name} {$financialbudget_year}</small></h1>
<form name="perform_budgeting/investmentfollowup_Form" id="perform_budgeting/headcount_Form"  action="#" method="post">
    <input type="hidden" id="identifier" name="identifier" value="{$sessionidentifier}">
    <table  style="width:100%;table-layout:fixed;">
        {$budgeting_header}
        {$output}
    </table
    <br/>

    <input type="{$type}" id="perform_budgeting/headcount_Button" value="{$lang->savecaps}" class="button"/>
</form>
<div id="perform_budgeting/headcount_Results"></div>
</body>