<div class="row">
    <div class="col-xs-12 col-lg-9 col-md-9">
        <h1>{$lang->manageusers}</h1>
    </div>
    <div class="col-xs-12 col-lg-3 col-md-3">
        {$createuser_button}
    </div>
</div>
<table class="datatable_basic table table-bordered row-border hover order-column" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>#</th>
            <th>{$lang->email}</th>
            <th>{$lang->usergroup}</th>
            <th>{$lang->lastvisit}</th>
            <th></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>#</th>
            <th>{$lang->email}</th>
            <th>{$lang->usergroup}</th>
            <th>{$lang->lastvisit}</th>
            <th></th>
        </tr>
    </tfoot>
    <tbody>
        {$userslist}
    </tbody>
</table>