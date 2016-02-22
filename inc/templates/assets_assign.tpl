<div class="container">
    <h1>{$lang->assign}</h1>
    <form name="perform_assets/assignassets_Form" id="perform_assets/assignassets_Form" enctype="multipart/form-data" method="post">
        <input type="hidden" name="auid" value="{$auid}" />
        <input type="hidden" value="do_{$actiontype}" name="action" id="action" />
        <div style="display:table; border-collapse:collapse; width:100%;">
            <div style="display:table-row;">
                <div style="display:table-cell; width:10%;">{$lang->assigners}</div>
                <div style="display:table-cell; width:100%; padding:5px;">{$employees_selectlist}</div>
            </div>
            <div style="display:table-row;">
                <div style="display:table-cell; width:10%;">{$lang->assetid}</div>
                <div style="display:table-cell; width:10%; padding:5px;">{$assets_selectlist}</div>
            </div>
            <div style="display:table-row;">
                <div style="display:table-cell; width:20%;">{$lang->from}</div>
                <div style="display:table-cell; width:50%; padding:5px;">
                    <input type="text" name="assignee[fromDate]"  value="{$assignee['fromDate_output']}" required="required" id="pickDateFrom" tabindex="3"/>
                    <input type="hidden" name="assignee[fromDate]" id="altpickDateFrom" value="{$assignee[fromDate_output]}"/>
                    <input name="assignee[fromTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="08:30" required="required" type="time">
                </div>
            </div>
            <div style="display:table-row;">
                <div style="display:table-cell; width:20%;">{$lang->to}</div>
                <div style="display:table-cell; width:50%; padding:5px;"><input type="text" name="assignee[toDate]"  value="{$assignee['toDate_output']}" required="required" id="pickDateTo" tabindex="4"/>
                    <input type="hidden" name="assignee[toDate]" id="altpickDateTo" value="{$assignee[toDate_output]}"/>
                    <input name="assignee[toTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="24:00" required="required" type="time">
                </div>
            </div>
            <div style="display:table-row; margin-top: 10px;" class="altrow"><div style="display:table-cell;">{$lang->assetcondition} </div></div>
            <div style="display:table-row;" class="altrow">
                <div style="display:table-cell; width:10%; vertical-align: top;">{$lang->conditionhandover}</div>
                <div style="display:table-cell; width:50%; padding:5px;"><textarea name="assignee[conditionOnHandover]" cols="80" rows='5'>{$assignee['conditionOnHandover']}</textarea></div>
            </div>
            <div style="display:table-row;" class="altrow">
                <div style="display:table-cell; width:10%; margin:20px; vertical-align: top;">{$lang->conditiononreturn}</div>
                <div style="display:table-cell; width:50%; padding:5px;"><textarea name="assignee[conditionOnReturn]" cols="80" rows='5'>{$assignee['conditionOnReturn']}</textarea></div>
            </div>
            <div style="display:table-row; width:100%;">
                <div style="display: table-cell;">
                    <input type="submit" class="button" value="{$lang->$actiontype}" id="perform_assets/assignassets_Button" />
                    <input type="reset" class="button" value="{$lang->reset}"/>
                </div>
            </div>
            <div style="display:table-row">
                <div style="display:table-cell;" id="perform_assets/assignassets_Results"></div>
            </div>
        </div>
    </form>
</div>