<div style="width:40%; display:inline-block;"><h1>{$lang->courses}</h1></div>
<table class="datatable_basic table table-bordered row-border hover order-column" data-checkonclick=true cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>{$lang->code}</th>
            <th>{$lang->title}</th>
            <th>{$lang->teacher}</th>
            <th>{$lang->description}</th>
            <th>{$lang->subscribed}</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>{$lang->code}</th>
            <th>{$lang->title}</th>
            <th>{$lang->teacher}</th>
            <th>{$lang->description}</th>
            <th>{$lang->subscribed}</th>
            <th>&nbsp;</th>
        </tr>
    </tfoot>
    <tbody>
        {$courses_list}
    </tbody>
</table>