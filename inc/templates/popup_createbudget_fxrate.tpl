<div id="popup_createbudgetfxrate" title="{$lang->createfxrate}">
    <form id="perform_budgeting/listfxrates_Form" name="perform_budgeting/listfxrates_Form"   method="post">
        <table>
            <tr>
            <input type="hidden" name="action" value="do_createrate" />
            <td>{$lang->affiliate}</td>
            <td>{$affiliate_list}</td>
            </tr>

            <tr> <td>{$lang->year}</td><td><select  name="budgetrate[year]"> {$budget_years}</select></td></tr>
            <tr>
                <td>{$lang->fromcurr}</td>
                <td> {$fromcurr_list}</td>
            </tr>
            <tr>
                <td>{$lang->tocurr}</td>
                <td> {$tocurr_list}</td>
            </tr>
            <tr>
                <td>{$lang->rate}</td>
                <td><input type="number" name="budgetrate[rate]"   step="any"  required="required" value="{$budgetrate->rate}"/></td>
            </tr>
            {$craetereverserate}


            <tr> <td><input type="submit" id="perform_budgeting/listfxrates_Button" value="{$lang->savecaps}" class="button"/></td></tr>
            <tr> <td><div id="perform_budgeting/listfxrates_Results"   /></div></td></tr>
        </table>



    </form>
</div>