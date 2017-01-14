<div class="panel panel-success" {$hide_coursedescription}>
    <div class="panel-heading">{$lang->students}</div>
    <div class="panel-body">
        <table class="datatable_basic table table-bordered row-border hover order-column" data-checkonclick=true cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>{$lang->subscribe}</th>
                    <th>{$lang->students}</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>{$lang->subscribe}</th>
                    <th>{$lang->students}</th>
                </tr>
            </tfoot>
            <tbody>
                {$studentsection_lines}
            </tbody>
        </table>
    </div>
</div>