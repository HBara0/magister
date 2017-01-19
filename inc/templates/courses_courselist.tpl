<div class="row">
    <div class="col-md-9 col-lg-9 col-sm-12">
        <h1>{$lang->courses}</h1>
    </div>
    <div class="col-md-3 col-lg-3 col-sm-12" {$hide_createcoursebutton}>
        <button type="button" class="btn btn-success" onclick="window.open('{$core->settings['rootdir']}/index.php?module=courses/managecourse', '_blank')">{$lang->cratecourse}
        </button>
    </div>
</div>
<table class="datatable_basic table table-bordered row-border hover order-column" data-checkonclick=true cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th>{$lang->code}</th>
            <th>{$lang->title}</th>
            <th>{$lang->teacher}</th>
            <th>{$lang->subscribed}</th>
            <th>{$lang->totalstudents}</th>
        </tr>
    </thead>
    <tbody>
        {$courses_list}
    </tbody>
</table>