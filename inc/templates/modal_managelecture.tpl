<div class="modal-dialog modal-lg" >
    <div class="modal-content">
        <div class="modal-header ">
            <h4 class="modal-title" >{$lang->managelecture}</h4>
        </div>
        <div class="modal-body">
            <form  action="#" method="post" id="perform_portal/calendar_Form" name="perform_portal/calendar_Form">
                <input type="hidden" name="lecture[inputChecksum]" value="{$lecture[inputChecksum]}">
                <input type="hidden" name="lecture[cid]" value="{$lecture[cid]}">
                <input type="hidden" name="lecture[lid]" value="{$lecture[lid]}">
                <input type="hidden" name="action" value="do_perform_managelecture">
                <div>
                    <h3>{$course_output}</h3>
                </div>
                <div class="form-group-sm">
                    <label for="title" style="font-weight: bold">{$lang->title}</label>
                    <input required="required" name='lecture[title]' value='{$lecture[title]}' type="text" class="form-control" id="title" placeholder="{$lang->title}">
                </div>
                <label>{$lang->daterange}<span style="color:red"> *</span></label>
                <div class="input-daterange input-group datepicker">
                    <input type="text" class="input-sm form-control" value="{$lecture[fromdateoutput]}" name="lecture[fromDate]" />
                    <span class="input-group-addon">to</span>
                    <input type="text" class="input-sm form-control" value="{$lecture[todateoutput]}" name="lecture[toDate]" />
                </div>
                <div class="input-group bootstrap-timepicker timepicker">
                    <input data-provide="timepicker" name="lecture[fromTime]" value="{$lecture[fromtimeoutput]}" type="text" class="form-control input-small">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                    <input data-provide="timepicker" name="lecture[toTime]" value="{$lecture[totimeoutput]}" type="text" class="form-control input-small">
                </div>
                <div class="form-group-lg">
                    <label>{$lang->isActive}</label>
                    {$isactivelist}
                </div>
            </form>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-xs-12">
                    <div id="perform_portal/calendar_Results"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-9 col-md-9 col-xs-12">
                    <input type="submit" value="{$lang->savecaps}" id="perform_portal/calendar_Button" class="button"/>
                </div>
            </div>
        </div>
    </div>
</div>