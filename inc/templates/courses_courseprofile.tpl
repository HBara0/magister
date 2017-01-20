<script>
    $(function() {
        $(document).on("click", "button[id^='subscribebutton']", function() {
            var id = $(this).attr("id").split("_");
            $.ajax({
                type: 'post',
                url: "{$core->settings['rootdir']}/index.php?module=courses/courseprofile&id=" + id[1] + "&action=course_" + id[2],
                data: "id=" + id[1],
                beforeSend: function() {
                    loadgif($("#subscribedive_" + id[1]));

                },
                success: function(returnedData) {
                    $("#subscribedive_" + id[1]).html(returnedData);
                }
            })
        });

    });
</script>
<div class="row">
    <div class="col-md-8 col-lg-8 col-sm-12">
        <h1>{$course_displayname}</h1>
        <h3>{$teacheroutput}</h3>
    </div>
    <div class="col-md-4 col-lg-4 col-sm-12" >
        <div class="btn-group" role="group" >
            <button {$hide_managecoursebutton} type="button" class="btn btn-success" onclick="window.open('{$editlink}', '_blank')">{$lang->managecourse}
            </button>
            {$course_folder}
        </div>
        {$addorremovecourse_button}
    </div>
</div>

<div class="panel panel-success" {$hide_coursedescription}>
    <div class="panel-heading">{$lang->coursedescription}</div>
    <div class="panel-body">
        {$course[description]}
    </div>
</div>
{$lecture_section}