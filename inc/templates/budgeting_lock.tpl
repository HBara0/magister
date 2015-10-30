<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->lockbudget}</title>
        {$headerinc}
    </script>

</head>
<body>
    {$header}
<tr>
    {$menu}
    <td class="contentContainer">
        <h1>{$lang->lockbudget}</h1>
        <form name="perform_budgeting/lockbudgets_Form" id="perform_budgeting/lockbudgets_Form" method="post">
            <div style="display:block; padding:8px;">
                <table width="100%">
                    <div class="thead" >{$lang->filterby}</div>

                    <tr>
                        <td  width="50%">
                            <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                <table class="datatable" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="100%"><input type="checkbox" id='affiliatefilter_checkall'><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->affiliate}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
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
                                            <th width="100%"><input type="checkbox" id='supplierfilter_checkall'><input class='inlinefilterfield' type='text' tabindex="2" placeholder="{$lang->search} {$lang->supplier}" style="width:70%;display:inline-block;margin-left:5px;"/></th>
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
                                            <th width="100%">{$lang->year}<input class='inlinefilterfield' type='text' placeholder="{$lang->search} {$lang->year}" style="width:70%;display:inline-block;margin-left:5px;"/></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$budget_year_list}
                                    </tbody>
                                </table>
                            </div>
                        </td>

                        <td  width="50%">
                            <div style="width:100%; height:70px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                <table class="datatable" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="100%">{$lang->type}<input class='inlinefilterfield' type='text' placeholder="{$lang->search} {$lang->type}" style="width:70%;display:inline-block;margin-left:5px;"/></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$budget_type_list}
                                    </tbody>
                                </table>
                            </div>
                        </td>

                    </tr>
                </table>
            </div>

            {$lang->lock}<input type="checkbox" name="budget[operation][lock]" value="1"/>
            {$lang->unlock}<input type="checkbox" name="budget[operation][unlock]" value="1"/>
            <br/><br/>
            <input type='button' id='perform_budgeting/lockbudgets_Button' value='{$lang->savecaps}' class='button'/>
            <div id="perform_budgeting/lockbudgets_Results"></div>
        </form>
    </td>
</tr>
</body>
</html>