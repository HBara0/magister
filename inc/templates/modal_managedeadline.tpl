<div class="modal-dialog modal-lg" >
    <div class="modal-content">
        <div class="modal-header ">
            <h4 class="modal-title" >{$lang->managedeadline}</h4>
        </div>
        <div class="modal-body">
            <form  action="#" method="post" id="perform_portal/calendar_Form" name="perform_portal/calendar_Form">
                <input type="hidden" name="deadline[inputChecksum]" value="{$deadline[inputChecksum]}">
                <input type="hidden" name="deadline[did]" value="{$deadline[did]}">
                <input type="hidden" name="action" value="do_perform_managedeadline">
                <div>
                    <h3>{$course_output}</h3>
                </div>
                <div class="form-group-sm">
                    <label for="title" style="font-weight: bold">{$lang->title}</label>
                    <input required="required" name='deadline[title]' value='{$deadline[title]}' type="text" class="form-control" id="title" placeholder="{$lang->title}">
                </div>
                <div class="input-daterange input-group datepicker">
                    <label>{$lang->time}<span style="color:red"> *</span></label>
                    <input type="text" class="input-sm form-control" value="{$deadline[fromdateoutput]}" name="deadline[fromDate]" />
                </div>
                <div class="input-group bootstrap-timepicker timepicker">
                    <input data-provide="timepicker" name="deadline[fromTime]" value="{$deadline[fromtimeoutput]}" type="text" class="form-control input-small">
                </div>
                <div class="form-group-sm">
                    <label for="description">{$lang->description}</label>
                    <div style="display:block;">
                        <textarea name="deadline[description]" cols="100" rows="6" id='description' class="basictxteditadv">
                            {$deadline[description]}
                        </textarea>
                    </div>
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