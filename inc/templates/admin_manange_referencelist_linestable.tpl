<div>
    <div id="lines">
        <table class="datatable">
            <input type="hidden" value="{$srlid}" name="list_id">
            <thead class="thead">
                <tr>
                    <th width="150px" class="border_right" rowspan="2" valign="top" align="center">{$lang->name}</th>
                    <th width="150px" class="border_right" rowspan="2" valign="top" align="center">{$lang->title}</th>
                    <th width="150px" class="border_right" rowspan="2" valign="top" align="center">{$lang->value}</th>
                    <th width="150px" class="border_right" rowspan="2" valign="top" align="center">{$lang->sequence}</th>
                    <th width="250px" class="border_right" rowspan="2" valign="top" align="center">{$lang->description}</th>
                    <th width="100px" class="border_right" rowspan="2" valign="top" align="center">{$lang->isactive}</th>
                </tr>
            </thead>
            <tbody id='lines_tbody'>
                {$select_lines}
            </tbody>
        </table>
        <div>
            <img src="{$core->settings['rootdir']}/images/add.gif" style="cursor: pointer" id="ajaxaddmore_managesystem/managereferencelist_lines" alt="Add">Add more lines
            <input type="hidden" name="numrows_lines" id="numrows_lines" value="{$l_rowid}" >
            <input type="hidden" name="moduletype_lines" id="moduletype_lines" value="manage" >
        </div>
    </div>
    <div id="table" style="display: none">
        <table class="datatable">
            <thead class="thead">
                <tr>
                    <th width="150px" class="border_right" rowspan="2" valign="top" align="center">{$lang->table}</th>
                    <th width="150px" class="border_right" rowspan="2" valign="top" align="center">{$lang->keycolumn}</th>
                    <th width="150px" class="border_right" rowspan="2" valign="top" align="center">{$lang->displayedcolumn}</th>
                    <th width="250px" class="border_right" rowspan="2" valign="top" align="center">{$lang->sqlwhereclause}</th>
                </tr>
            </thead>
            <tbody>
                {$select_table}
            </tbody>
        </table>
    </div>
</div