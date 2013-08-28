<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillsurvey}</title>
        {$headerinc}
        <script type="text/javascript">
            $(document).ready(function() {
                $('input[name=fxrate]').live('change', function() {
                    $('#fxrateinput').slideToggle('fast', function() {
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
            <h3>{$lang->generatebudget}</h3>
            <form name="perform_budgeting/generatebudget_Form" id="perform_budgeting/generatebudget_Form" method="post" action="index.php?module=budgeting/preview&amp;identifier={$core->input[identifier]}&amp;referrer=generate">

                <div style="display:block; padding:8px;">
                    <div style="display:inline-block;padding:10px; vertical-align:top;">{$lang->affiliate}</div>
                    <div style="display:inline-block;padding:10px;vertical-align:central;">{$affiliated_budget}</div>

                    <div style="display:inline-block;padding:10px;vertical-align:top;">{$lang->supplier}</div>
                    <div style="display:inline-block;padding:10px;vertical-align:central;">{$budget_supplierslist}</div>
                    <div style="display:inline-block;padding:10px;vertical-align:top;">{$lang->bm}</div>
                    <div style="display:inline-block;padding:10px;vertical-align:top;"> <select name="budget[managers][]"  size="6" multiple="multiple" tabindex="3"> {$business_managerslist}</select></div>
                </div>

                <div style="display:block; padding:8px;"> 
                    <div style="display:inline-block;padding:10px;vertical-align:top;">{$lang->segment}</div>
                    <div style="display:inline-block;padding:10px;">  <select name="budget[segments][]" multiple="multiple" tabindex="4"> {$budget_segment}</select> </div>
                    <div style="display:inline-block;padding:10px;vertical-align:top;">{$lang->year}</div>
                    <div style="display:inline-block;padding:10px;vertical-align:top;"><select name="budget[years][]" id="year" multiple="multiple" tabindex="5" >{$budget_year}</select></div>
                </div>
                <div style="display:block; padding:15px;">
                    <div style="display:inline-block;padding:10px;">{$lang->curr}</div>
                    <div style="display:inline-block;padding:10px;">{$budget_currencylist}</div>
                </div>
                <div id="budget_currautomatic" style="display:block;">
                    <div style="display:inline-block;padding:8px; margin-left:65px;"><input  id="autofxrate"  type="radio" value=1 checked name="fxrate"/></div>
                    <div style="display:inline-block;padding:8px;">{$lang->automaticusdrate}</div>
                </div>
                <div  id="budget_currspecify"style="display:block;">
                    <div style="display:inline-block;padding:8px; margin-left:65px;"><input  id="specifyfxrate" type="radio" value=2 name="fxrate"/></div>
                    <div style="display:inline-block;padding:8px;"> {$lang->specifyusdrate}<span id="fxrateinput" style="display:none;"><input type="texr" size="8" name="budgetgenerate[fxrate]"/></span> 

                    </div>
                </div>

                <div  id="budget_currspecify"style="display:block;">
                    
                
                    <input type="submit" value="{$lang->generate}" class="button"> <input type="reset" value="{$lang->reset}" class="button">
                </div>
            </form>
        </td>
    </tr>
</body>
</html>