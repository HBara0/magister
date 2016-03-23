<h1>{$lang->createbudget}</h1>
<form name="perform_budgeting/create_Form" id="perform_budgeting/createbudget_Form" action="index.php?module=budgeting/fillbudget&amp;stage=fillbudgetline" method="post">
    <input type="hidden" name="identifier" value="{$sessionidentifier}"/>
    <div style="display:block;">
        <div style="display:inline-block;padding:8px;">{$lang->affiliate}</div>
        <div style="display:inline-block;padding:8px;">{$affiliated_budget}</div>
    </div>
    <div id="budget_supplier" style="display:block;">
        <div style="display:inline-block;padding:8px;">{$lang->supplier}</div>
        <div style="display:inline-block;padding:8px;">{$budget_supplierslist}</div> <a href="index.php?module=contents/addentities&amp;type=supplier&amp;referrer=budgeting" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a> <div id="supplierslist_Loading" style="display:inline-block;padding:8px;"></div>
    </div>
    <div  id="budget_year" style="display:block;">
        <div style="display:inline-block;padding:8px;">{$lang->year}</div>
        <div style="display:inline-block;padding:8px; margin-left:20px;"><select name="budget[year]" title="year" id="year">{$budget_year}</select></div>
        <div id="years_Loading" style="display:inline-block;padding:8px;"></div>
    </div>
    <div  id="buttons_row" style=" display: none;clear:left;"><input type="submit" value="Proceed" class="button"  /></div>
</div>
</form>
<div id="perform_budgeting/createbudget_Results"></div>
