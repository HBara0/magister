<div class="modal-dialog" >
    <div class="modal-content">
        <div class="modal-header ">
            <h4 class="modal-title" >{$lang->viewrecommendation}</h4>
        </div>
        <div class="modal-body">
            <form  action="#" method="post" id="perform_events/manageevent_Form" name="perform_events/manageevent_Form">
                <input type="hidden" name="event[rid]" value="{$recommendation[rid]}">
                <input type="hidden" name="event[isPublic]" value="0">
                <input type="hidden" name="event[type]" value="2">

                <div class="form-group-sm">
                    <label for="title" style="font-weight: bold">{$lang->title}<span style="color:red"> *</span></label>
                    <input required="required" name='event[title]' value='{$recommendation[displayname]}' type="text" class="form-control" id="title" placeholder="{$lang->title}">
                </div>
                <label>{$lang->daterange}<span style="color:red"> *</span></label>
                <div class="input-daterange input-group datepicker">
                    <input type="text" class="input-sm form-control" value="{$event[fromdateoutput]}" name="event[fromDate]" />
                    <span class="input-group-addon">to</span>
                    <input type="text" class="input-sm form-control" value="{$event[todateoutput]}" name="event[toDate]" />
                </div>
                <div class="input-group bootstrap-timepicker timepicker">
                    <input data-provide="timepicker" name="event[fromTime]" value="{$event[fromtimeoutput]}" type="text" class="form-control input-small">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                    <input data-provide="timepicker" name="event[toTime]" value="{$event[totimeoutput]}" type="text" class="form-control input-small">
                </div>
                <div class="form-group-sm">
                    <label for="description">{$lang->description}</label>
                    <div style="display:block;">
                        <textarea name="event[description]" cols="100" rows="6" id='description' class="basictxteditadv">
                            {$recommendation[description_output]}
                        </textarea>
                    </div>
                </div>
                <div class="row">
                    <input type="submit" value="{$lang->savecaps}" id="perform_events/manageevent_Button" class="button"/>
                    <div id="perform_events/manageevent_Results"></div>
                </div>
            </form>
        </div>
    </div>
</div>
