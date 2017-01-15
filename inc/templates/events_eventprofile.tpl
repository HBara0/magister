<script>
    $(function() {
        $(document).on("click", "button[id^='subscribebutton']", function() {
            var id = $(this).attr("id").split("_");
            $.ajax({
                type: 'post',
                url: "{$core->settings['rootdir']}/index.php?module=courses/courseprofile&id=1&action=course_" + id[2],
                data: "id=" + id[1],
                beforeSend: function() {
                    $("#subscribedive_" + id[1]).html("<img src='{$core->settings[rootdir]}/images/preloader.gif'>");
                },
                success: function(returnedData) {
                    $("#subscribedive_" + id[1]).html(returnedData);
                }
            })
        });

    });
</script>
<div class="row">
    <div class="col-md-9 col-lg-9 col-sm-12">
        <h1>{$event_displayname}</h1>
        <h4>{$daterangeoutput}</h4>
    </div>
    <div class="col-md-3 col-lg-3 col-sm-12" >
        <div class="btn-group">
            <button {$hide_managecoursebutton} type="button" class="btn btn-success" onclick="window.open('{$editlink}', '_blank')">{$lang->managecourse}
            </button>
            {$course_folder}
        </div>
        {$addorremovecourse_button}
    </div>
</div>
</div>

<div class="panel panel-success" {$hide_eventdescription}>
    <div class="panel-heading">{$lang->eventdescription}</div>
    <div class="panel-body">
        {$event[description]}
    </div>
</div>
{$lecture_section}