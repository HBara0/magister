<div class="row">
    <div class="col-md-9 col-lg-9 col-sm-12">
        <h1>{$course_displayname}</h1>
        <h3>{$teacheroutput}</h3>
    </div>
    <div class="col-md-3 col-lg-3 col-sm-12" {$hide_managecoursebutton}>
        <button type="button" class="btn btn-success" onclick="window.open('{$editlink}', '_blank')">{$lang->managecourse}
        </button>
    </div>
</div>

<div class="panel panel-success" {$hide_coursedescription}>
    <div class="panel-heading">{$lang->coursedescription}</div>
    <div class="panel-body">
        {$course[description]}
    </div>
</div>

<div class="panel panel-success" {$hide_coursedescription}>
    <div class="panel-heading">{$lang->coursedescription}</div>
    <div class="panel-body">
        {$course[description]}
    </div>
</div>
{$lecture_section}
{$course_folder}