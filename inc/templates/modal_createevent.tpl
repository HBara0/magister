<script>
    $(function() {
        $(document).on("click", "button[id^='subscribebutton']", function() {
            var id = $(this).attr("id").split("_");
            $.ajax({
                type: 'post',
                url: "{$core->settings['rootdir']}/index.php?module=events/eventslist&id=" + id[1] + "&action=events_" + id[2],
                data: "id=" + id[1],
                beforeSend: function() {
                    loadgif($("#subscribedive_" + id[1]));

                },
                success: function(returnedData) {
                    $("#subscribedive_" + id[1]).html(returnedData);
                }
            })
        });
        manage_typeshow();
        $('.type_select').on('change', function() {
            manage_typeshow();

        })
        function manage_typeshow() {
            var value = $(".type_select").val();
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
            <h4 class="modal-title" >{$lang->addcalendarevent}</h4>
        </div>
        <div class="modal-body">
            <form  action="#" method="post" id="perform_portal/calendar_Form" name="perform_portal/calendar_Form">
                <input type="hidden" name="event[inputChecksum]" value="{$event[inputChecksum]}">

                <label>{$lang->type}</label>
                <div class="form-group-lg">
                    <select name="type" class="select2_basic type_select">
                        <option {$event_selected} value="event">{$lang->event}</option>
                        <option {$deadline_selected}value="deadline">{$lang->deadline}</option>
                    </select>
                </div>
                <div class="form-group-sm">
                    <label for="title" style="font-weight: bold">{$lang->title}<span style="color:red"> *</span></label>
                    <input required="required" name='event[title]' value='{$event[displayname]}' type="text" class="form-control" id="title" placeholder="{$lang->title}">
                </div>
                <label>{$lang->daterange}</label>
                <div class="input-daterange input-group datepicker">
                    <input type="text" class="input-sm form-control" value="{$event[fromdateoutput]}" name="event[fromDate]" />
                    <span class="input-group-addon">to</span>
                    <input data-typeshow="event" type="text" class="input-sm form-control" value="{$event[todateoutput]}" name="event[toDate]" />
                </div>
                <div class="input-group bootstrap-timepicker timepicker">
                    <input data-provide="timepicker" name="event[fromTime]" value="{$event[fromtimeoutput]}" type="text" class="form-control input-small">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                    <input data-typeshow="event" data-provide="timepicker" name="event[toTime]" value="{$event[totimeoutput]}" type="text" class="form-control input-small">
                </div>
            </form>
        </div>
        <div class="form-group-sm">
            <label for="description">{$lang->description}</label>
            <div style="display:block;">
                <textarea name="event[description]" cols="100" rows="6" id='description' class="basictxteditadv">
                    {$event[description_output]}
                </textarea>
            </div>
        </div>
        <div class="modal-footer">
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