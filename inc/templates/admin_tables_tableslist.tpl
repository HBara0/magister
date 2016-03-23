<h1>{$lang->tableslist}</h1>
<form method='post' action='$_SERVER[REQUEST_URI]'>
    <div style="float:right;" class="subtitle"><a href="#" id="showpopup_addsystable" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->addtable}</a></div>
    <table width="100%" border="0" cellspacing="0" cellpadding="2" style="display:table" class="datatable">
        <thead>
            <tr>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->tablename}</th>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->classname}</th>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->numberofcolumns}</th>
                <th>&nbsp;</th>
            </tr><tr>
                {$filters_row}
            </tr>
        </thead>
        <tbody>
            {$tableslist_rows}
        </tbody>
    </table>
</form>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
        {$popup_addtable}
