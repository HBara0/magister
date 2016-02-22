<h3>{$lang->typeslist}</h3>
<form action='index.php?module=facilitymgmt/typeslist' method="post">
    <div style="float:right;" class="subtitle"> <a target="_blank" href="{$core->settings[rootdir]}/index.php?module=facilitymgmt/managefacilitytype" ><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->create}</a></div>
    <table class="datatable" width="100%">
        <thead>
            <tr>
                <th width="22%">{$lang->title} </th>
                <th width="40%">{$lang->typecategory} </th>
                <th width="7%">{$lang->isactive}</th>
                <th width="5%"></th>
            </tr>
            {$filters_row}
        </thead>
        <tbody>
            {$facilitytypes_rows}
        </tbody>
    </table>
</form>
