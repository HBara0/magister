<div id="popup_createeventtask" title="{$lang->createeventtask}">
    <div class="ui-state-highlight ui-corner-all" style="padding: 5px; margin-bottom: 10px;">{$lang->create}: <a href="#popup_createeventtask" onClick="$('#createevent_fields').show();
            $('#createtask_fields').hide();">{$lang->event}</a> | <a href="#popup_createeventtask" onClick="$('#createtask_fields').show();
                    $('#createevent_fields').hide();
                    $('#type').val('task');">{$lang->task}</a></div>
    <div id="createevent_fields">
        <iframe id='uploadFrame' name='uploadFrame' style="display:none;" src='#'></iframe>
        <form method="post" enctype="multipart/form-data" action="index.php?module=calendar/eventstasks" target="uploadFrame">
            <input type="hidden" id="action" name="action" value="do_createeventtask" />
            <input type="hidden" id="type" name="type" value= "event" />
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
            <div style="width:20%; margin-bottom: 20px; display:inline-block; vertical-align:top;">{$lang->description}</div><div style="width:70%; display:inline-block;"><textarea cols="50" rows="10" name="event[description]" class='basictxteditadv' id="eventdescription" required="required"></textarea></div><br />
            <br />
            <div class="subtitle">{$lang->announceoptions}</div>
            {$ispublic_checkbox}
            {$restriction_selectlist}
            {$notifyevent_checkbox}
            <div class="subtitle">{$lang->publishoptions}</div>
            <div style="cursor: pointer;"><a onClick="$('#eventemployess').fadeToggle();"><span class="subtitle">{$lang->invitemployees}...</span></a></div>
            <div style="width:100%; height:100px; overflow:auto; transition: background-color 0.5s ease; display:none; padding:5px; z-index:2;" id="eventemployess">
                {$invitees_rows}
            </div>
            <br />
            <fieldset class="altrow2" style="border:1px solid #DDDDDD" title="{$lang->attachments}">
                <legend class="subtitle">{$lang->attachments}</legend>
                <input name="attachments[]" id="attachments" multiple="true" type="file" />
            </fieldset>
            <fieldset class="altrow2" style="border:1px solid #DDDDDD">
                <legend class="subtitle">{$lang->eventlogo}</legend>
                <input name="logo[]" id="logo" multiple="false" type="file" />
            </fieldset>
            <hr />
            <input type="submit" class="button" value="{$lang->create}" id="calendar_eventstasks" onclick="$('#upload_Result').show()"  />
            <div id="upload_Result" style="display:none;"><img src="{$core->settings[rootdir]}/images/loading.gif" /> {$lang->uploadinprogress}</div>
        </form>
    </div>

    <div id="createtask_fields" style="display:none;">
        <form name="perform_calendar/eventstasks_Form" id="perform_calendar/eventstasks_Form" action="#" method="post">
            <input type="hidden" id="action" name="action" value="do_createtask" />
            <input type="hidden" id="type" name="type" value="task" />

            <div class="subtitle">{$lang->taskdetails}</div>
            <div style="width:20%; display:inline-block;"><strong>{$lang->task}</strong></div><div style="width:70%; display:inline-block;"><input type="text" name="task[subject]" size="50" required='required'/></div><br />
            <div style="width:20%; display:inline-block;"><strong>{$lang->duedate}</strong></div><div style="width:70%; display:inline-block;"><input type="text" name="task[altDueDate]" id="pickDate_duedate" value="{$duedate}" /></div>
            <input type="hidden" name="task[dueDate]" id="altpickDate_duedate" value="" />
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
            <div style="width:20%; display:inline-block;">{$lang->prerequisite}</div><div style="width:70%; display:inline-block;">
                <input style="width: 100%" type="text" {$disabled}  id="alltasks_autocomplete" autocomplete="false" tabindex="1" value="{$taskname}" required="required"/>
                <input type='hidden' id='alltasks_id'  name="task[prerequisitTask]" value="{$task[prerequisitTask]}"/>
                <input type='hidden' id='alltasks_id_output' name="task[prerequisitTask]" value="{$task[prerequisitTask]}" disabled/>
            </div>
            <div style="width:20%; display:inline-block; vertical-align:top;">{$lang->description}</div><div style="width:70%; display:inline-block;"><textarea cols="50" rows="10" name="task[description]" class="basictxteditadv" id="taskdescription"></textarea></div><br/>
            <div style="width:20%; display:inline-block; vertical-align:top;">{$lang->reminder}</div><div style="width:70%; display:inline-block;"><input type="text" value="" tabindex="1" autocomplete="off" name="task[reminderStart]"id="pickDate_reminderStart" size="15"/> {$lang->repeat} {$reminderinterval_selectlist}</div>
            <div style="width:20%; display:inline-block; vertical-align:top;">{$lang->notifytask}</div><div style="width:70%; display:inline-block;">{$tasks_notify_radiobutton}</div><br/>
            <br />
            <div class="subtitle">{$lang->sharewith}</div>
            {$task_sharewith}
            <input type='button' class='button' value='{$lang->create}' id='perform_calendar/eventstasks_Button' />
            <div id="perform_calendar/eventstasks_Results"> </div>
        </form>
    </div>
</div>