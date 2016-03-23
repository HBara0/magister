<h1>{$lang->chemicalsubstances}</h1>
<table class="datatable" width="100%" style="table-layout:fixed;">
    <thead>
        <tr>
            <td class="thead">{$lang->casnum}</td>
            <td class="thead">{$lang->checmicalproduct}</td>
            <td class="thead">{$lang->synonyms}</td>
        </tr>
    </thead>
    {$chemicalslist_section}
    <tr>
        <td colspan="3">
            <div style="width:40%; float:left; margin-top:0px;">
                <form method='post' action='$_SERVER[REQUEST_URI]'>
                    {$lang->perlist}:
                    <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
                </form>
            </div>
        </td>
    </tr>
</table>
