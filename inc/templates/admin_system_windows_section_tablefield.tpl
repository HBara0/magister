<form name="fields_{$tabnum}_managesystem/managewindows_Form" id="fields_{$tabnum}_managesystem/managewindows_Form" action="#" method="post">
    <table>
        <tr>
        <div>
            <table cellspacing="0" cellpadding="2" class="datatable">
                <thead class="thead">
                    <tr>
                        <th class=" border_right" rowspan="2" valign="top" align="center"><div style="width:200px">{$lang->name}</div></th>
                <th class=" border_right" rowspan="2" valign="top" align="center"><div style="width:200px">{$lang->dbcolumn}</div></th>
                <th class=" border_right" rowspan="2" valign="top" align="center"><div style="width:150px">{$lang->isdisplayed}</div></th>
                <th class=" border_right" rowspan="2" valign="top" align="center"><div style="width:150px">{$lang->isreadonly}</div></th>
                <th class=" border_right" rowspan="2" valign="top" align="center"><div style="width:200px">{$lang->sequence}</div></th>
                <th class=" border_right" rowspan="2" valign="top" align="center"><div style="width:200px">{$lang->fieldtype}</div></th>
                <th class=" border_right" rowspan="2" valign="top" align="center"><div style="width:200px">{$lang->list}</div></th>
                <th class=" border_right" rowspan="2" valign="top" align="center"><div style="width:200px">{$lang->length}</div></th>
                <th class=" border_right" rowspan="2" valign="top" align="center"><div style="width:350px">{$lang->displaylogic}</div></th>
                <th class=" border_right" rowspan="2" valign="top" align="center"><div style="width:350px">{$lang->description}</div></th>
                <th class=" border_right" rowspan="2" valign="top" align="center"><div style="width:350px">{$lang->comment}</div></th>
                <th class=" border_right" rowspan="2" valign="top" align="center"><div style="width:350px">{$lang->onchangefunction}</div></th>
                <th class=" border_right" rowspan="2" valign="top" align="center"><div style="width:200px">{$lang->allowedfiletypes}</div></th>
                </tr>
                </thead>
                <tbody id="fields_{$swstid}_tbody">
                    {$section_fields}
                </tbody>
                <tr><td>
                        <div id='addmore_sectionfields_div' {$disable_morerows} >
                            <img src="{$core->settings['rootdir']}/images/add.gif" style="cursor: pointer" id="ajaxaddmore_managesystem/managewindows_fields_{$swstid}" alt="Add">Add more fields
                            <input type="hidden" name="numrows_fields" id="numrows_fields" value="{$fieldrow_id}" >
                            <input type="hidden" name="ajaxaddmoredata[swsid]" id="ajaxaddmoredata_fields" value="{$swsid}"/>
                            <input type="hidden" name="ajaxaddmoredata[swstid]" id="ajaxaddmoredata_fields" value="{$tabnum}"/>
                            <input type="hidden" name="moduletype_fields" id="moduletype_fields_{$swstid}" value="manage" >
                        </div>
                    </td>
                </tr>
                <tr>
                <div style="float: left">
                    <input {$disable_fieldsave} type='submit' form="fields_{$tabnum}_managesystem/managewindows_Form" style="cursor: pointer" class='button' value="{$lang->savefields}" id="fields_{$tabnum}_managesystem/managewindows_Button">
                </div>
                </tr>
            </table>
            </form>
            <div id="fields_{$tabnum}_managesystem/managewindows_Results"></div>
