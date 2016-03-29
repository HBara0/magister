<h1>{$lang->fillmontlyreport}</h1>
<form action="index.php?module=reporting/previewmreport&amp;identifier={$identifier}" method="post" id="perform_reporting/fillmreport_Form" name="perform_reporting/fillmreport_Form">
    <input type="hidden" name="referrer" value="fill" />
    {$rid_field}
    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td colspan="2"><span class="subtitle">{$lang->mreportidentification}</span></td>
        </tr>
        <tr class="altrow2">
            <td width="20%"><span class='font-weight: bold;'>{$lang->supplier}</span></td>
            <td><input type='text' id='supplier_1_autocomplete' name='suppliername' value="{$report_data[suppliername]}" autocomplete="off"/>
                <input type="text" size="3" id="supplier_1_id_output" value="{$report_data[spid]}" disabled/>
                <input type='hidden' id='supplier_1_id' name='spid' value="{$report_data[spid]}"/>
                <a href="index.php?module=contents/addentities&amp;type=supplier" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a>
                <div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div></td>
        </tr>
        <tr class="altrow2">
            <td><span class='font-weight: bold;'>{$lang->affiliate}</span></td>
            <td>{$affiliates_list}</td>
        </tr>
        <tr class="altrow2">
            <td><span class='font-weight: bold;'>{$lang->month}/{$lang->year}</span></td>
            <td>{$month_list}
                <input type="text" id="year" name="year" size="4" value="{$report_data[year]}"/></td>
        </tr>
        <tr>
            <td colspan="2"><hr /></td>
        </tr>
        <tr>
            <td valign="top" colspan="2"><span class="subtitle">{$lang->overallstatus}</span></td>
        </tr>
        <tr>
            <td colspan="2">
                <table cellpadding="0" border="0" cellspacing="0" style="margin:0px;">
                    <tbody id="overallstatus_tbody">
                        {$overallstatus_fields}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"><img src="images/add.gif" id="addmore_overallstatus_generic" alt="{$lang->add}">
                                <input type="hidden" name="overallstatus_numrows" id="overallstatus_numrows" value="{$overallstatusrownumber}">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight:bold;">{$lang->personalconsiderations}:</td>
        </tr>
        <tr>
            <td colspan="2">
                <textarea name="considerations" id="considerations" cols="50" rows="8">{$report_data[considerations]}</textarea></td>
        </tr>
        <tr>
            <td colspan="2"><hr /></td>
        </tr>
        <tr>
            <td valign="top" colspan="2"><span class="subtitle">{$lang->keycustomers}</span></td>
        </tr>
        <tr>
            <td colspan="2">
                <table cellpadding="0" border="0" cellspacing="0" style="margin:0px;">
                    <tbody id="keycustomers_tbody">
                        {$keycustomers_fields}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"><img src="images/add.gif" id="addmore_keycustomers_customer" alt="{$lang->add}">
                                <input type="hidden" name="keycustomers_numrows" id="keycustomers_numrows" value="{$keycustomersrownumber}">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"><hr /></td>
        </tr>
        <tr>
            <td valign="top" colspan="2"><span class="subtitle">{$lang->accomplishmentsplans}</span></td>
        </tr>
        <tr>
            <td colspan="2">
                <p>
                    {$lang->accomplishmentsreportingperiod}:<br />
                    <textarea name="accomplishments" id="accomplishments" cols="50" rows="8">{$report_data[accomplishments]}</textarea>
                </p>
                <p>
                    {$lang->actionstoimplement}:<br />
                    <textarea name="actions" id="action" cols="50" rows="8">{$report_data[actions]}</textarea>
                </p>
            </td>
        </tr>
    </table>
    <hr />
    <div align="center"><input type="button" class="button" value="{$lang->savecaps}" id="perform_reporting/fillmreport_Button"><input type="submit" value="{$lang->nextcaps}" class="button"> <input type="reset" value="{$lang->reset}" class="button"></div>
</form>

<div id='perform_reporting/fillmreport_Results'></div>
