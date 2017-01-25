<script>
    $(function() {
        manage_typeshow();
        $('#type_select').on('change', function() {
            manage_typeshow();

        })
        function manage_typeshow() {
            var value = $("#type_select").val();
            $("[data-typeshow]").each(function(i, obj) {
                if ($(obj).data('typeshow') == value) {
                    $(obj).removeAttr('disabled');
                    $(obj).show();
                }
                else {
                    $(obj).hide();
                    $(obj).attr('disabled', 'disabled');
                }
            });
        }
    });
</script>
<div class="modal-dialog modal-lg" >
    <div class="modal-content">
        <div class="modal-header ">
            <div class="row">
                <h4 class="modal-title" >{$title}</h4>
            </div>
        </div>
        <div class="modal-body">
            <form  action="#" method="post" id="perform_courses/courses_Form" name="perform_courses/courses_Form">
                <input type="hidden" name="action" value="save_lecturedeadline">
                <input type="hidden" name="event[cid]" value="{$cid}">
                <input type="hidden" name="event[inputChecksum]" value="{$event[inputChecksum]}">
                <div class="form-group-lg" {$hidetype}>
                    <label>{$lang->type}</label>
                    <select name="type" class="select2_basic" id="type_select">
                        <option {$lecture_selected} value="lecture">{$lang->lecture}</option>
                        <option {$deadline_selected}value="deadline">{$lang->deadline}</option>
                    </select>
                </div>
                <label>{$lang->daterange}<span style="color:red"> *</span></label>
                <div class="input-daterange input-group datepicker">
                    <input type="text" class="input-sm form-control" value="{$event[fromdateoutput]}" name="event[fromDate]" />
                    <span  class="input-group-addon">to</span>
                    <input data-typeshow="lecture" type="text" class="input-sm form-control" value="{$event[todateoutput]}" name="event[toDate]" />
                </div>
                <div class="input-group bootstrap-timepicker timepicker">
                    <input data-provide="timepicker" name="event[fromTime]" value="{$event[fromtimeoutput]}" type="text" class="form-control input-small">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                    <input data-typeshow="lecture" data-provide="timepicker" name="event[toTime]" value="{$event[totimeoutput]}" type="text" class="form-control input-small">
                </div>
                <div class="form-group-lg">
                    <label for="title" style="font-weight: bold">{$lang->title}</label>
                    <input required="required" name='event[title]' value='{$event[title]}' type="text" class="form-control" id="title" placeholder="{$lang->title}">
                </div>
                <div class="form-group-lg" data-typeshow="lecture">
                    <label for="title" style="font-weight: bold">{$lang->location}</label>
                    <input required="required" name='event[location]' value='{$event[location]}' type="text" class="form-control" id="title" placeholder="{$lang->location}">
                </div>
                <label>{$lang->isActive}</label>
                <div class="form-group-lg">
                    {$isactive_list}
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-xs-12">
                    <div id="perform_courses/courses_Results"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-9 col-md-9 col-xs-12">
                    <input type="submit" value="{$lang->savecaps}" id="perform_courses/courses_Button" class="button"/>
                </div>
            </div>
        </div>
    </div>
</div>