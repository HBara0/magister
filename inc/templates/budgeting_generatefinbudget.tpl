<div class="container">
    <h1>{$lang->generatefinbudget}</h1>
    <form name="perform_budgeting/generatefinbudget_Form" id="perform_budgeting/generatefinbudget_Form" method="post" action="index.php?module=budgeting/previewfinbudget">
        <input type="hidden" name="identifier" value="{$core->input[identifier]}"/>
        <div style="display:block; padding:8px;">
            <table>
                <div class="thead" >{$lang->filterby}</div>

                <tr>
                    <td  width="50%">
                        <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                            <table class="datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th width="100%">{$lang->affiliate}<input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->affiliate}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
                                    </tr>
                                </thead>
                                <tbody >
                                    {$affiliates_list}
                                </tbody>
                            </table>
                        </div>
                    </td>
                    <td  width="50%">
                        <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                            <table class="datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th width="100%">{$lang->budgettype}<input class='inlinefilterfield' type='text'placeholder="{$lang->search} {$lang->type}" style="width:40%;display:inline-block;margin-left:5px;"/></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$budgetypes_list}
                                </tbody>
                            </table>
                        </div>
                    <td>
                </tr>
                <tr>
                    <td  width="50%">
                        <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                            <table class="datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th width="100%">{$lang->year}<input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->year}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
                                    </tr>
                                </thead>
                                <tbody >
                                    {$budget_year_list}
                                </tbody>
                            </table>
                        </div>
                    </td>
                    <td  width="50%">
                        <div style="width:100%; height:100px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">

                            <table class="datatable" width="100%">
                                <thead>
                                    <tr>
                                        <th width="100%">{$lang->convercurr}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            {$currencies_list}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>



            </table>

        </div>
        <div  id="budget_currspecify"style="display:block;">
            <input type="submit" value="{$lang->generate}" class="button"> <input type="reset" value="{$lang->reset}" class="button">
        </div>
    </form>
</div>