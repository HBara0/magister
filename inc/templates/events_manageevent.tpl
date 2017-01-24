
<div class="row">
    <div class="col-md-9 col-lg-9 col-sm-12">
        <h1>{$lang->manageevent}</h1>
    </div>
    <div class="col-md-3 col-lg-3 col-sm-12">
        <button type="button" class="btn btn-success" onclick="window.open('{$core->settings['rootdir']}/index.php?module=events/manageevent', '_blank')">{$lang->crateevent}
        </button>
    </div>
</div>
<form  action="#" method="post" id="perform_events/manageevent_Form" name="perform_events/manageevent_Form">
    <input type="hidden" name="event[eid]" value="{$event[eid]}">
    <input type="hidden" name="event[inputChecksum]" value="{$event[inputChecksum]}">

    <div class="form-group-lg">
        <label for="title" style="font-weight: bold">{$lang->eventtitle}<span style="color:red"> *</span></label>
        <input required="required" name='event[title]' value='{$event[title]}' type="text" class="form-control" id="title" placeholder="{$lang->eventtitle}">
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
    <label>{$lang->isActive}</label>
    <div class="form-group-lg">
        {$isactive_list}
    </div>
    <div class="form-group-lg">
        <label for="description">{$lang->description}</label>
        <div style="display:block;">
            <textarea name="event[description]" cols="100" rows="6" id='description' class="basictxteditadv">
                {$event[description]}
            </textarea>
        </div>
    </div>
    {$studentsubscription_section}
    <input type="submit" value="{$lang->savecaps}" id="perform_events/manageevent_Button" class="button"/>
    <div id="perform_events/manageevent_Results"></div>

</form>
