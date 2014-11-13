<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->bank}</title>
        {$headerinc}
        <script type="text/javascript">

        </script>
    </head>
    <body>
    <tr style="width:100%;">

        {$header}
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>{$lang->bank}<br /><small>{$affiliate->name} - {$budget_data['year']}</small></h1>
                {$output_currency}
            <form name="perform_budgeting/bank_Form" id="perform_budgeting/bank_Form"  action="#" method="post">
                <input type="hidden" id="identifier" name="identifier" value="{$sessionidentifier}">
                <table class="datatable" width="100%">
                    <thead style="width:100%;">
                        {$bank_header}
                        {$bank_row}
                    </thead>
                    <tbody id="bankfacilities{$rowid}_tbody" style="width:100%;">
                        {$row}
                    </tbody>
                    <tfoot>
                        <tr><td valign="top">
                                <input name="numrows_bankfacilities{$rowid}" type="hidden" id="numrows_bankfacilities{$rowid}" value="{$rowid}">
                                <input type="hidden" name="ajaxaddmoredata[affid]" id="ajaxaddmoredata_affid" value="{$budget_data[affid]}"/>
                                <input type="hidden" name="ajaxaddmoredata[year]" id="ajaxaddmoredata_year" value="{$budget_data['year']}"/>
                                <img src="./images/add.gif" id="ajaxaddmore_budgeting/bank_bankfacilities_{$rowid}" alt="{$lang->add}">
                            </td>
                        </tr>
                    </tfoot>

                </table>
                <hr />

                <input type="submit" id="perform_budgeting/bank_Button" value="Save" class="button"/>
            </form>
            <div id="perform_budgeting/bank_Results"></div>
        </td>
    </tr>
    {$footer}
</body>
</html>