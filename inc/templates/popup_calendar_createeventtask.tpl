<div id="popup_createeventtask" title="{$lang->createeventtask}">
    <form name='perform_calendar/eventstasks_Form' id="perform_calendar/eventstasks_Form" method="post">
        <input type="hidden" id="action" name="action" value="do_createeventtask" />
        <input type="hidden" id="type" name="type" value="event" />
        <div class="ui-state-highlight ui-corner-all" style="padding: 5px; margin-bottom: 10px;">{$lang->create}: <a href="#popup_createeventtask" onClick="$('#createevent_fields').show();$('#createtask_fields').hide();$('#type').val('event');">{$lang->event}</a> | <a href="#popup_createeventtask" onClick="$('#createtask_fields').show();$('#createevent_fields').hide();$('#type').val('task');">{$lang->task}</a></div>
        <div id="createtask_fields" style="display:none;">
        	<div class="subtitle">{$lang->taskdetails}</div>
  			<div style="width:20%; display:inline-block;">{$lang->task}</div><div style="width:70%; display:inline-block;"><input type="text" name="task[subject]" size="50"/> <input type="hidden" name="task[dueDate]" id="altpickDate_duedate" value="" /></div><br />
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
        	<div style="width:20%; display:inline-block;">{$lang->title}</div><div style="width:70%; display:inline-block;"><input type="text" name="event[title]" size="50" /></div><br />
       		<div style="width:20%; display:inline-block;">{$lang->type}</div><div style="width:70%; display:inline-block;">{$eventypes_selectlist}</div><br />
            <div style="width:20%; display:inline-block;">{$lang->between}</div><div style="width:70%; display:inline-block;"><input type="text" id="pickDate_eventfromdate" autocomplete="off" tabindex="2" value="" /><input type="hidden" name="event[fromDate]" id="altpickDate_eventfromdate" value=""/> & <input type="text" id="pickDate_eventtodate" autocomplete="off" tabindex="2" value="" /><input type="hidden" name="event[toDate]" id="altpickDate_eventtodate" value=""/></div><br />
        	<div style="width:20%; display:inline-block;">{$lang->location}</div><div style="width:70%; display:inline-block;"><input type="text" name="event[place]" maxlength="300"  size="50"/></div><br />
        	<div style="width:20%; display:inline-block; vertical-align:top;">{$lang->description}</div><div style="width:70%; display:inline-block;"><textarea cols="50" rows="10" name="event[description]"></textarea></div><br />
			{$ispublic_checkbox}
            {$restriction_selectlist}
            {$notifyevent_checkbox}
			{$publishonwebsite_checkbox}
        </div>
        <input type='button' class='button' value='{$lang->create}' id='perform_calendar/eventstasks_Button' />
    </form>
    <hr />
    <div id="perform_calendar/eventstasks_Results" ></div>
</div>