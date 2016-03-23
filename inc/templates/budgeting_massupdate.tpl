<td class="container">
    <script type="text/javascript">

        $(function() {
            $(document).on('click', 'input[id="agreement"]', function() {
                $('input[type="button"][id^="perform_"]').attr("disabled", !this.checked);
            });

        });

    </script>
    <h1>{$lang->$pagename}</h1>
    <div class="ui-state-highlight ui-corner-all"  style=" border-color:red;padding-left: 5px; margin-bottom:12px;">
        <p><h2 style="font-size: 19px;  font-weight: bolder; color:red">Warnings</h2><strong>{$lang->warningupdate}:</strong> </p>
</div>
<form name="perform_budgeting/{$pagename}_Form" id="perform_budgeting/{$pagename}_Form" method="post" >
    <input name="action" type="hidden"  value="do_{$pagename}"/>

    <div style="display: block;" id="budgetsfilter">
        <div class="thead">Filter by ID</div>
        <div>
            <textarea cols="50" rows="5" name="budget[filterline][{$lineidname}]" placeholder="Seperate IDs by comma"></textarea>
        </div>
    </div>
    <div style="display: block;" id="budgetsfilter">
        <div class="thead">Budgets Filters</div>
        <table width="100%">
            <tr>
                <td width="50%">
                    <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr>
                                    <th width="100%"><input type="checkbox" id="affiliate_checkall" />{$lang->affiliate}<input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->affiliate}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
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
                                    <th width="100%"><input type="checkbox" id="supplier_checkall" />{$lang->supplier}<input class='inlinefilterfield' type='text' tabindex="2" placeholder="{$lang->search} {$lang->supplier}" style="width:70%;display:inline-block;margin-left:5px;"/></th>
                                </tr>
                            </thead>
                            <tbody>
                                {$suppliers_list}
                            </tbody>
                        </table>
                    </div>
                </td>

            </tr>
            <tr>
                <td  width="50%">
                    <div style="width:100%; height:70px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr>
                                    <th width="100%"><input type="checkbox" id="year_checkall" />{$lang->year}<input class='inlinefilterfield' type='text' placeholder="{$lang->search} {$lang->year}" style="width:70%;display:inline-block;margin-left:5px;"/></th>
                                </tr>
                            </thead>
                            <tbody>
                                {$budget_year_list}
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

    </div>
    <div style="display: block;" id="budgetlinesfilter">
        <div class="thead">Budget Lines Filters</div>
        <table width="100%">
            <Tr>
                <td  width="50%">
                    <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr>
                                    <th width="100%"><input type="checkbox" id="bm_checkall"/>{$lang->manager}<input class='inlinefilterfield' type='text' placeholder="{$lang->search} {$lang->bm}" style="width:60%;display:inline-block;margin-left:5px;"/></th>
                                </tr>
                            </thead>
                            <tbody style="height:100px;">
                                {$business_managerslist}
                            </tbody>
                        </table>
                    </div>
                </td>
                <td  width="50%">
                    <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr>
                                    <th width="100%"><input type="checkbox" id="saletype_checkall"/>{$lang->saletype}<input class='inlinefilterfield' type='text' placeholder="{$lang->search} {$lang->saletype}" style="width:60%;display:inline-block;margin-left:5px;"/></th>
                                </tr>
                            </thead>
                            <tbody style="height:100px;">
                                {$sale_types}
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="thead">Values to Overwrite:</div>

    <table width="100%" class="datatable datacell">

        {$overwrite_fields}
    </table>

    <div style="padding:8px"> <input type="checkbox" id="agreement"/>{$lang->agreewarningcond}</div>
    <div><input type="button"   disabled="disabled"  id="perform_budgeting/{$pagename}_Button" value="{$lang->savecaps}" class="button"/></td</div>
</form>
<div id="perform_budgeting/{$pagename}_Results" value="{$lang->savecaps}"/></div>
</div>