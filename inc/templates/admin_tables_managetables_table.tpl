<form name="perform_managesystem/managetables_Form" method="post" id="perform_managesystem/managetables_Form" >
    <table width="100%" border="0" cellspacing="0" cellpadding="2" style="display:table" class="datatable">
        <input type='hidden' id='stid' value='{$table_data['stid']}' name='stid'>
        <thead>
            <tr>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->columnname}</th>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->columnsystemname}</th>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->columntitle}</th>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->datatype}</th>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->length}</th>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->primarykey}</th>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->unique}</th>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->required}</th>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->simple}</th>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->displayname}</th>
                <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->references}</th>
            </tr>
        </thead>
        <tbody>
            {$table_details}
        </tbody>
    </table>
    <div style="display:inline-block;"><input type="submit" class="button" value="{$lang->save}" id="perform_managesystem/managetables_Button" /> </div>
    <div style="display:inline-block;"><input type="reset" class="button" value="{$lang->reset}"/></div>
    <hr>
    <div>
        <table>
            <th class="header" colspan="3">
                <span class="subtitle">{$lang->createormodclass}</span>
            </th>
            <tr>
                <td>{$lang->classdefinition}</td><td><input type="checkbox" checked="checked" value="1" id='classdef' name="classdef"></td>
            </tr>
            <tr>
                <td>{$lang->classfunctions}</td><td><input type="checkbox" checked="checked" value="1" id='classfunc' name="classfunc"></td>
            </tr>
            <tr>
                <td>{$lang->overwriteclassif}</td><td><input type="checkbox" value="1" id='overwrite' name="overwrite"></td>
            </tr>
        </table>
        <div style="display:inline-block;"><button class="button" id="save_createclass" >{$lang->saveandcreateclass}</button></div>
    </div>
</form>
<div style="display: inline-block" id="perform_managesystem/managetables_Results"></div>
