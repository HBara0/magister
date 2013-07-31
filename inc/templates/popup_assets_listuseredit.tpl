<div id="popup_edituser" title="{$lang->edituserassets}">
        <form name="perform_assets/assignassets_Form" id="perform_assets/assignassets_Form"  method="post">
            <input type="hidden" name="auid" id="auid" value="{$core->input[id]}" />
            <input type="hidden" value="do_edit" name="action" id="action" />
        
                <div style="display:table; border-collapse:collapse; width:100%;">
                <div style="display:table-row;">
                    <div style="display:table-cell; width:10%;">{$lang->assigners}</div>
                    <div style="display:table-cell; width:100%; padding:5px;">{$employees_list} </div>
                </div>
                
                    <div style="display:table-row;">
                    <div style="display:table-cell; width:10%;">{$lang->assetid}</div>
                    <div style="display:table-cell; width:10%; padding:5px;">{$assets_list}</div>
                </div>

             
                  <div style="width:20%; display:inline-block;">{$lang->from}</div>
                    <div style="display:table-cell; width:10%; padding:5px;">
                        <input type="text" name="assignee[fromDate]" {$disable_text} value="{$assignee['fromDate_output']}"required="required" id="pickDateFrom" tabindex="3"/>
                        <input name="assignee[fromTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" {$disable_text} value="{$assignee['fromTime_output']}" placeholder="08:30" required="required" type="time">
                   </div>
             
                <div style="display:table-row;">
                    <div style="display:table-cell; width:10%;">{$lang->to}</div>
                    <div style="display:table-cell; width:100%; padding:5px;"><input type="text" name="assignee[toDate]" {$disable_text} value="{$assignee['toDate_output']}" required="required" id="pickDateTo" tabindex="4"/>
                     <input name="assignee[toTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" {$disable_text} value="{$assignee['toTime_output']}" placeholder="05:00" required="required" type="time">
                    </div>
                </div>

                    <div style="display:table-row;">
                    <div style="display:table-cell; width:10%;">{$lang->conditionhandover}</div>
                    <div style="display:table-cell; width:100%; padding:5px;"><textarea name="assignee[conditionOnHandover]" {$disable_text}>{$assignee['conditionOnHandover']}</textarea>
                    
                    </div>
                </div>
                
                        <div style="display:table-row;">
                    <div style="display:table-cell; width:10%; margin:20px;">{$lang->conditiononreturn}</div>
                    <div style="display:table-cell; width:100%; padding:5px;"><textarea name="assignee[conditionOnReturn]" {$disable_cor}>{$assignee['conditionOnReturn']}</textarea>
                    
                    </div>
                </div>
                    
                <div style="display:table-row;">
                    <div style="display: table-cell; width:35%;">
                        <input type="submit" class="button" value="{$lang->actiontype}" id="perform_assets/assignassets_Button" />
                        <input type="reset" class="button"   onclick="$('#popup_edituser').dialog('close')"value="{$lang->close}"/>
                    </div>
                </div>

                <div style="display:table-row;width:25%;">
                    <div style="display:table-cell;width:25%;" id="perform_assets/assignassets_Results"></div>
                </div>
                </div>
         </form>
    </div>