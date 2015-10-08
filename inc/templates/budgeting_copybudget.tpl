<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->copybudget}</title>
        {$headerinc}
        <script>
            $(function() {
                $(document).on('change', 'select[id="affid"]', function() {
                    $.ajax({
                        method: "post",
                        url: "index.php?module=budgeting/copybudgets&action=get_businessmgrs",
                        data: "id=" + $(this).val(),
                        beforeSend: function() {
                        },
                        complete: function() {
                        },
                        success: function(returnedData) {
                            $("#from_bm").html(returnedData);
                            $("#to_bm").html(returnedData);
                        }

                    });
                });
            });
        </script>

    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->copybudget}</h1>
            <form name="perform_budgeting/copybudgets_Form" id="perform_budgeting/copybudgets_Form" action="#" method="post">
                <input type="hidden" name="identifier" value="{$sessionidentifier}"/>
                <div style="display:block;">
                    <div style="display:inline-block;padding:8px;">{$lang->affiliate}</div>
                    <div style="display:inline-block;padding:8px;">{$affiliated_budget}</div>
                </div>
                <div id="budget_supplier" style="display:block;">
                    <div style="display:inline-block;padding:8px;">{$lang->supplier}</div>
                    <div style="display:inline-block;padding:8px;">{$budget_supplierslist}</div>
                </div>
                <div  id="budget_year" style="display:block;">
                    <div style="display:inline-block;padding:8px;">{$lang->year}</div>
                    <div style="display:inline-block;padding:8px; margin-left:20px;">
                        {$budget_year}
                    </div>
                </div>

                <div  id="budget_year" style="display:block;">
                    <div style="padding:8px;font-weight: bold;">{$lang->bm}</div>
                    <div style="display:inline-block;padding:8px;">{$lang->from}</div>
                    <div style="display:inline-block;padding:8px; margin-left:20px;">
                        {$frombm_list}
                    </div>
                    <div style="display:inline-block;padding:8px;">{$lang->to}</div>
                    <div style="display:inline-block;padding:8px; margin-left:20px;">
                        {$tobm_list}
                    </div>
                </div>


                <input type="submit" value="Save" id="perform_budgeting/copybudgets_Button" class="button"  /></div>
        </div>
    </form>
    <div id="perform_budgeting/copybudgets_Results"></div>
</td>
</tr>
{$footer}
</body>
</html>