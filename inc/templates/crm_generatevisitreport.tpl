<script type="text/javascript">
    $(function() {
        var selectLists = new Array();
        selectLists[0] = new Array(new Array("spid", "uid"), new Array("cid", "uid"), new Array("spid", "cid"));
        selectLists[1] = new Array("cid", "spid", "uid");

        $("#showBy_1,#showBy_2,#showBy_3").click(function() {
            var selectValue = $(this).val() - 1;

            for(var i = 0; i < selectLists[0][selectValue].length; i++) {
                var selectName = selectLists[0][selectValue][i        ];
                $("select[id='" + selectName + "']").attr({multiple: 'true', size:5, name: selectName + '[]', id: selectName + '[]'});
            }
            $("select[id='" + selectLists[1][selectValue] + "[]']").removeAttr("multiple");
            $("select[id='" + selectLists[1][selectValue] + "[]']").removeAttr("size");
            $("select[id='" + selectLists[1][selectValue] + "[]']").attr({name: selectLists[1][selectValue], id: selectLists[1][selectValue]});
        });
    });
</script>
<h1>{$lang->generatevisitsreports}</h1>
<form method="post" action="index.php?module=crm/generatevisitreport&amp;action=generate">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td colspan="3"><span class="subtitle">{$lang->generatetype}</span></td>
        </tr>
        <tr>
            <td colspan="3">
                <input type="radio" name="generateType" id="generateType_1" value="1" onClick="$('#aggregate_options').hide();" tabindex="1" checked /> {$lang->generatestatisticalreport} <br />
                <input type="radio" name="generateType" id="generateType_2" value="2" onClick="$('#aggregate_options').show();" tabindex="2"/> {$lang->generateaggregatedreport}
                <hr />
            </td>
        </tr>
        <tr>
            <td colspan="3"><span class="subtitle">{$lang->dataaggregationcriteria}</span></td>
        </tr>
        <tr>
            <td>
                <input type="radio" name="showBy" id="showBy_1" value="1" tabindex="3" checked /> {$lang->showbycustomer}<br />
                <input type="radio" name="showBy" id="showBy_3" value="3" tabindex="4" /> {$lang->showbyemployee}
            </td>
            <td colspan="2"><input type="radio" name="showBy" id="showBy_2" value="2" tabindex="5" /> {$lang->showbysupplier}</td>
        </tr>
        <tr>
            <td colspan="3"><hr /></td>
        </tr>
        <tr>
            <td colspan="3"><span class="subtitle">{$lang->selectdaterange}</span></td>
        </tr>
        <tr>
            <td>{$lang->fromdate} <input type="text" id="pickDate_from" autocomplete="off" tabindex="6" /><input type="hidden" name="fromDate" id="altpickDate_from" /> </td>
            <td colspan="2">{$lang->todate} <input type="text" id="pickDate_to" autocomplete="off" tabindex="7" /><input type="hidden" name="toDate" id="altpickDate_to" /> </td>
        </tr>
        <tr>
            <td colspan="3"><br /><span class="subtitle">{$lang->informationfor}</span></td>
        </tr>
        <tr>
            <td valign="top">
                <span style="font-weight:bold">{$lang->customer}</span><br>
                {$customers_list}
            </td>
            <td valign="top">
                <span style="font-weight:bold">{$lang->supplier}</span><br>
                {$suppliers_list}
            </td>
            <td valign="top">
                <span style="font-weight:bold">{$lang->employee}</span><br>
                {$employees_list}
            </td>
        </tr>
        <tr id="aggregate_options" style="display:none;">
            <td colspan="3">
                <table>
                    <tr>
                        <td colspan="2">
                            <span class="subtitle">{$lang->customisereport}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->showlimitedcustdetails}</td>
                        <td colspan="2">
                            <input name="showLimitedCustDetails" id="showLimitedCustDetails" value="1" checked="checked" type="radio"> {$lang->yes} <input name="showLimitedCustDetails" id="showLimitedCustDetails" value="0" type="radio"> {$lang->no}

                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->incvisitdetails}</td>
                        <td colspan="2">
                            <input name="incVisitDetails" id="incVisitDetails" value="1" checked="checked" type="radio"> {$lang->yes} <input name="incVisitDetails" id="incVisitDetails2" value="0" type="radio"> {$lang->no}
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->inccompetitiondetails}</td>
                        <td colspan="2">
                            <input name="incCompetitionDetails" id="incCompetitionDetails" value="1" checked="checked" type="radio"> {$lang->yes} <input name="incCompetitionDetails" id="incCompetitionDetails2" value="0" type="radio"> {$lang->no}
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->inccompetcomments}</td>
                        <td colspan="2">
                            <input name="incCommentsCompetition" id="incCommentsCompetition" value="1" checked="checked" type="radio"> {$lang->yes} <input name="incCommentsCompetition" id="incCommentsCompetition2" value="0" type="radio"> {$lang->no}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr />
    <input type="submit" class="button" value="{$lang->generatereport}">
</form>