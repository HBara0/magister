<h1>{$lang->supplierslist}</h1>
<table class="datatable_basic table table-bordered row-border hover order-column" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th style="width:30%;">{$lang->companyname}</th>
            <th style="width:35%;">{$lang->affiliate}</th>
            <th style="width:35%;">{$lang->segment}</th>
        </tr>
    </thead>
    <tfoot
        <tr>
            <th style="width:30%;">{$lang->companyname}</th>
            <th style="width:35%;">{$lang->affiliate}</th>
            <th style="width:35%;">{$lang->segment}</th>
        </tr>
    </tfoot>
    <tbody>
        {$suppliers_list}
    </tbody>
</table>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>