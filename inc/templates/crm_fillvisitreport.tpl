<script>
    $(function() {
        $('input[id^=customer_]').bind('change', function() {
            if(sharedFunctions.checkSession() == false) {
                return;
            }
            var cid = $('[id$=id_output]').val();
            sharedFunctions.requestAjax("post", "index.php?module=crm/fillvisitreport&action=get_customerlocation", "&cid=" + cid, 'content_detailsloader', 'content_details', true);
        });
    });</script>
<h1>{$lang->fillvisitreport}</h1>
{$draftreports_selectlist}
<form action="index.php?module=crm/fillvisitreport&amp;stage=visitdetails&amp;identifier={$identifier}" method="post">
    <input type="hidden" name="referrer" value="fill">
    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td>{$lang->calltype}</td>
            <td><select name="type" id="type">
                    <option value="1"{$type_selected[1]}>{$lang->facetoface}</option>
                    <option value="2"{$type_selected[2]}>{$lang->telephonecall}</option>
                </select></td>
            <td rowspan="2" valign="top" width="50%">{$lang->dateofvisit} &nbsp; <input type="text" id="pickDate" autocomplete="off" value="{$visitreport_values[date_output]}"/><input type="hidden" name="date" id="altpickDate" value="{$visitreport_values[date_formatted]}" /></td>
        </tr>
        <tr>
            <td>{$lang->callpurpose}</td>
            <td><select name="purpose" id="purpose">
                    <option value="1"{$purpose_selected[1]}>{$lang->followup}</option>
                    <option value="2"{$purpose_selected[2]}>{$lang->service}</option>
                    <option value="3"{$purpose_selected[3]}>{$lang->prospective}</option>
                </select></td>
        </tr>
    </table>
    <p><hr /></p>
<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td colspan="2"><span class="subtitle">{$lang->definition} Definition</span></td>
    </tr>
    <tr>
        <td>{$lang->customername}</td>
        <td><input type='text' id='allcustomertypes_1_autocomplete' value="{$visitreport_values[customername]}" autocomplete="off"/><input type="text" size="3" id="allcustomertypes_1_id_output" value="{$visitreport_values[cid]}" disabled/><input type='hidden' id='allcustomertypes_1_id' name='cid' value="{$visitreport_values[cid]}"/><a href="index.php?module=contents/addentities&amp;type=customer" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a><div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div>
            <div id="content_detailsloader"></div>
            <div id="content_details"></div>
        </td>
    </tr>
    <tr>
        <td>{$lang->contactperson}</td>
        <td><input type='text' id='representative_2_autocomplete' value="{$visitreport_values[representativename]}" autocomplete="off"/><input type="text" size="3" id="representative_2_id_output" value="{$visitreport_values[rpid]}" disabled/><input type='hidden' id='representative_2_id' name='rpid' value="{$visitreport_values[rpid]}" /><a href="#" id="addnew_crm/fillvisitreport_representative"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a><div id='searchQuickResults_2' class='searchQuickResults' style='display:none;'></div></td>
    </tr>
    <tr>
        <td>{$lang->affiliate}</td>
        <td>{$affiliates_list}</td>
    </tr>
    <tr>
        <td>{$lang->productlinediscussed}</td>
        <td>{$productline_list}</td>
    </tr>
    <tr>
        <td valign="top">{$lang->suppliername}</td>
        <td style="padding:0px;">
            <table cellpadding="0" border="0" cellspacing="0" style="margin:0px;">
                <tbody id="visitsuppliers_tbody">
                    {$suppliers_fields}
                </tbody>
                <tfoot>
                    <tr>
                        <td><img src="images/add.gif" id="addmore_visitsuppliers_supplier" alt="{$lang->add}" onClick="$('#accompaniedby_row').hide();"> <input type="hidden" name="visitsuppliers_numrows" id="numrows" value="{$supplierrownumber}"></td>
                    </tr>
                </tfoot>
            </table>
        </td>
    </tr>
    <tr id="accompaniedby_row">
        <td>{$lang->accompaniedby}</td>
        <td><input type='text' id='supprepresentative_4_autocomplete' value="{$visitreport_values[srepresentativename]}" autocomplete="off"/><input type="text" size="3" id="supprepresentative_4_id_output" value="{$visitreport_values[sprpid]}" disabled/><input type='hidden' id='supprepresentative_4_id' name='sprpid' value="{$visitreport_values[sprpid]}" /><a href="#" id="addnew_crm/fillvisitreport_supprepresentative"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a><div id='searchQuickResults_4' class='searchQuickResults' style='display:none;'></div></td>
    </tr>
    <tr><td colspan="2"><hr /></td></tr>
    <tr>
        <td colspan="2">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td>{$lang->availabilityissues}</td>
                    <td><select name="availabilityIssues" id="availabilityIssues">
                            <option value="1"{$availabilityIssues_selected[1]}>{$lang->available}</option>
                            <option value="2"{$availabilityIssues_selected[2]}>{$lang->underspotshortage}</option>
                            <option value="3"{$availabilityIssues_selected[3]}>{$lang->usuallyundershortage}</option>
                        </select></td>
                    <td>{$lang->supplystatus}</td>
                    <td><select name="supplyStatus" id="supplyStatus">
                            <option value="1"{$supplyStatus_selected[1]}>{$lang->regular}</option>
                            <option value="2"{$supplyStatus_selected[2]}>{$lang->onspotbasis}</option>
                            <option value="3"{$supplyStatus_selected[3]}>{$lang->usedto}</option>
                            <option value="4"{$supplyStatus_selected[4]}>{$lang->never}</option>
                        </select></td>
                </tr>
                <tr>
                    <td>{$lang->ourcurrentmktshare}</td>
                    <td colspan="3"><input type="text" name="currentMktShare" id="currentMktShare" accept="numeric" value="{$visitreport_values[currentMktShare]}"/></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div align="center"><input type="submit" value="{$lang->next}" class="button"> <input type="reset" value="{$lang->reset}" class="button"></div>
</form>