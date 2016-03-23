<h1>{$lang->referencelists}</h1>
<form method='post' action='$_SERVER[REQUEST_URI]'>
    <div style="width: 75%">
        <div style="float:right;" class="subtitle"><a  target="_blank"  href="{$core->settings['rootdir']}/manage/index.php?module=managesystem/managereferencelist"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->addlist}</a></div>
        <table width="100%" border="0" cellspacing="0" cellpadding="2" style="display:table" class="datatable">
            <thead>
                <tr>
                    <th width="200px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->name}</th>
                    <th width="200px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->referencetype}</th>
                    <th width="200px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->selecttype}</th>
                    <th>&nbsp;</th>
                </tr><tr>
                    {$filters_row}
                </tr>
            </thead>
            <tbody>
                {$ref_list_row}
            </tbody>
        </table>
    </div>
</form>
<div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>

{$popup_addtable}
