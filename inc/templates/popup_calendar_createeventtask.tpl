<div id="popup_createeventtask" title="{$lang->createeventtask}">
    <form name='perform_calendar/eventstasks_Form' id="perform_calendar/eventstasks_Form" method="post">
        <input type="hidden" id="action" name="action" value="do_createeventtask" />
        <input type="hidden" id="type" name="type" value="event" />
        <div class="ui-state-highlight ui-corner-all" style="padding: 5px; margin-bottom: 10px;">{$lang->create}: <a href="#popup_createeventtask" onClick="$('#createevent_fields').show();
                $('#createtask_fields').hide();
                $('#type').val('event');">{$lang->event}</a> | <a href="#popup_createeventtask" onClick="$('#createtask_fields').show();
                $('#createevent_fields').hide();
                $('#type').val('task');">{$lang->task}</a></div>
        <div id="createtask_fields" style="display:none;">
            <div class="subtitle">{$lang->taskdetails}</div>
            <div style="width:20%; display:inline-block;"><strong>{$lang->task}</strong></div><div style="width:70%; display:inline-block;"><input type="text" name="task[subject]" size="50" required='required'/> <input type="hidden" name="task[dueDate]" id="altpickDate_duedate" value="" /></div><br />
            <div style="width:20%; display:inline-block;">{$lang->assignedto}</div><div style="width:70%; display:inline-block;">{$assignedto_selectlist}</div><br />
            <div style="width:20%; display:inline-block;">{$lang->priority}</div><div style="width:70%; display:inline-block;"><select name="task[priority]"><option value="2">{$lang->priorityhigh}</option><option value="1" selected='selected'>{$lang->prioritynormal}</option><option value="0">{$lang->prioritylow}</option></select></div><br />
            <div style="width:20%; display:inline-block; vertical-align:top;">{$lang->completed}</div><div style="width:70%; display:inline-block;">
                <select name="task[percCompleted]">
                    <option value="0">0%</option>
                    <option value="25">25%</option>
                    <option value="50">50%</option>
                    <option value="75">75%</option>
                    <option value="100">100%</option>
                </select>
            </div><br/>
            <div style="width:20%; display:inline-block; vertical-align:top;">{$lang->description}</div><div style="width:70%; display:inline-block;"><textarea cols="50" rows="10" name="task[description]" class="texteditormin"></textarea></div><br/>
            <div style="width:20%; display:inline-block; vertical-align:top;">{$lang->reminder}</div><div style="width:70%; display:inline-block;"><input type="text" value="" tabindex="1" autocomplete="off" name="task[reminderStart]"id="pickDate_reminderStart" size="15"/> {$lang->repeat} {$reminderinterval_selectlist}</div>
            <div style="width:20%; display:inline-block; vertical-align:top;">{$lang->notifytask}</div><div style="width:70%; display:inline-block;">{$tasks_notify_radiobutton}</div><br/>
        </div>
        <div id="createevent_fields">
            <div class="subtitle">{$lang->eventdetails}</div>
            <div style="width:20%; display:inline-block;"><strong>{$lang->title}</strong></div><div style="width:70%; display:inline-block;"><input type="text" name="event[title]" size="50" required='required' /></div><br />
            <div style="width:20%; display:inline-block;"><strong>{$lang->type}</strong></div><div style="width:70%; display:inline-block;">{$eventypes_selectlist}</div><br />
            <div id="visittypefields" style="display:none;width:100%;"> 
                <div style="display:block;width:100%;"> 
                    <div style="width:20%; display:inline-block;"><strong>{$lang->affiliate}</strong></div>
                    <div style="width:30%; display:inline-block;">{$eventaffiliates_selectlist}</div><br />
                </div>
                <div style="display:block;width:100%"> 
                    <div style="width:20%; display:inline-block;"><strong>{$lang->supplier}</strong></div>
                    <div style="width:30%; display:inline-block;">{$suppliers_selectlist}</div><br />
                </div>
            </div>
            <div style="width:20%; display:inline-block;"><strong>{$lang->fromdate}</strong></div>          
            <div style="width:70%; display:inline-block;">
                <input type="text" id="pickDate_eventfromdate" autocomplete="off" tabindex="2" value="" required='required' />
                <input type="hidden" name="event[fromDate]" id="altpickDate_eventfromdate" value=""/>       
                <input type="time" name="event[fromTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="{$current_date[hours]}:{$current_date[minutes]}" required="required">  
            </div>
            <div style="width:20%; display:inline-block;"><strong>{$lang->todate}</strong></div>  
            <div style="width:70%; display:inline-block;">
                <input type="text" id="pickDate_eventtodate" autocomplete="off" tabindex="2" value="" required='required' />
                <input type="hidden" name="event[toDate]" id="altpickDate_eventtodate" value=""/>
                <input type="time" name="event[toTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="{$current_date[hours]}:{$current_date[minutes]}" required="required">
            </div>
            <br />
            <div style="width:20%; display:inline-block;"><strong>{$lang->location}</strong></div><div style="width:70%; display:inline-block;">
                <input type="text" value="{$affiliate_address}" name="event[place]" maxlength="300" required='required' size="50"/>
            </div><br />
            <div style="width:20%; margin-bottom: 20px; display:inline-block; vertical-align:top;">{$lang->description}</div><div style="width:70%; display:inline-block;"><textarea cols="50" rows="10" name="event[description]"></textarea></div><br />
            <div class="subtitle" style="cursor: pointer;"><a onClick="$('#eventemployess').fadeToggle();">{$lang->invitemployees}...</a></div>
            <div style="width:100%; height:100px; overflow:auto; transition: background-color 0.5s ease; display:none; padding:5px; z-index:2;" id="eventemployess">
                {$invitees_rows}
            </div>
            <br />
            <div class="subtitle">{$lang->announceoptions}</div>
            {$ispublic_checkbox}
            {$restriction_selectlist}
            {$notifyevent_checkbox}
            <div class="subtitle">{$lang->publishoptions}</div>
            {$publishonwebsite_checkbox}
        </div>
        <input type='submit' class='button' value='{$lang->create}' id='perform_calendar/eventstasks_Button' />
    </form>
    <hr />
    <div id="perform_calendar/eventstasks_Results" ></div>
</div>