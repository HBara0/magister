<h1>{$lang->fillfinancialbudget}</h1>
<form action="index.php" method="post">
    <input type="hidden" name="module" value="budgeting/financialadminexpenses" id="module_hiddenfield">
    <input type="hidden" name="identifier" value="{$sessionidentifier}"/>
    <div style="display:block;">
        <div style="display:inline-block;padding:8px;">{$lang->affiliate}</div>
        <div style="display:inline-block;padding:8px;">{$affiliated_budget}</div>
    </div>
    <div  id="budget_year" style="display:block;">
        <div style="display:inline-block;padding:8px;">{$lang->year}</div>
        <div style="display:inline-block;padding:8px; margin-left:20px;"><select name="financialbudget[year]" title="year" id="year" >{$budget_year}</select></div>
        <div id="years_Loading" style="display:inline-block;padding:8px;"></div>
    </div>
    <div style="display:block;">
        <div style="display:inline-block;padding:8px;">{$lang->budgettype}</div>
        <div style="display:inline-block;padding:8px;">{$budgettypes_list}</div>
    </div>
    <div>
        <hr />
        <input type="submit" value="Proceed" class="button"/>
    </div>
</form>
