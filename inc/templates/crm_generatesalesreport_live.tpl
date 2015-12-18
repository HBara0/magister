<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->generatesalesreport}</title>
        {$headerinc}
        <script type="text/javascript">
            $(function() {
                $(document).on('change', "select[id='type']", function() {
                    $("div[id^='salesreport_endofmonth']").show();

                    var id = $(this).attr("id")
                    var value = $(this).attr("value")
                    $("div[id$=_reporttype]").not([id ^= '" + $(this).val() + "']).hide();
                    $("div[id^='" + value + "']").show(1000);
                    if(value == 'endofmonth') {
                        $("div[id^='salesreport_endofmonth']").hide();
                    }
                });
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->generatesalesreport}</h1>
            <div style="margin-left: 5px;">
                <form name="perform_crm/salesreport_Form" id="perform_crm/salesreportlive_Form" method="post" class="hidden-print">
                    {$lang->type} <select name="type" id="type">
                        <option value="analytic">{$lang->analytic}</option>
                        <option value="dimensional">{$lang->dimensional}</option>
                        <option value="transactions">{$lang->transactions}Transactions</option>
                        <option value="endofmonth">{$lang->endofmonth}</option>

                    </select>
                    <div id="salesreport_endofmonth">

                        <div style="display:inline-block; width:20%; vertical-align:top;margin-right:30px;">{$lang->currency} {$currencies_list}</div>

                        <fieldset style="vertical-align:top; margin-top:10px; margin-bottom:10px;" class="altrow2">
                            <legend>{$lang->filters}</legend>
                            <div style="display:inline-block; width:45%; vertical-align:top;">{$lang->affiliate}<br />{$affiliates_list}</div>
                        </fieldset>
                        <div>
                            <div style="display:inline-block; width:10%; vertical-align:top;">{$lang->fromdate}</div><div style="display:inline-block; width:20%; vertical-align:top;"><input type="text" id="pickDate_from" autocomplete="off" tabindex="1" /> <input type="hidden" name="fromDate" id="altpickDate_from" /></div>
                            <div style="display:inline-block; width:10%; vertical-align:top;">{$lang->todate}</div><div style="display:inline-block; width:20%; vertical-align:top;"><input type="text" id="pickDate_to" autocomplete="off" tabindex="2" /> <input type="hidden" name="toDate" id="altpickDate_to" /></div>
                            <div style="display:inline-block; width:10%; vertical-align:top;">{$lang->fxtype}</div><div style="display:inline-block; width:20%; vertical-align:top;">{$fxtypes_selectlist}<br />Or specify: <input type='number' step="any" name="fxrate" ></div>
                        </div>
                        <div>
                            <input type="checkbox" name="generatecharts" value="1" checked="checked">$lang->generateclassificationcharts
                        </div>
                    </div>
                    <hr />
                    <div id="dimensional_reporttype" style="display:none;">
                        <div class="thead" style="margin:10px;">{$lang->dimensions}</div>
                        <div style="display:block; ">
                            <div style="display:inline-block;width:40%;  vertical-align:top;">
                                <ul id="dimensionfrom" class="sortable">
                                    {$dimension_item}
                                </ul>
                            </div>
                            <div style="display:inline-block;width:40%; vertical-align:top;">
                                <div style="text-align: left;">
                                    {$lang->selecteddimensions}<br />
                                    <ul id="dimensionto" class="sortable">
                                        <li class="sortable-placeholder" style="background:none;">{$lang->drophere}</li>
                                    </ul>
                                </div>
                                <input type='hidden' id='dimensions' name="salereport[dimension][]" value=''>
                            </div>
                        </div>
                    </div>
                    <input type="submit" id="perform_crm/salesreportlive_Button" value="{$lang->generatereport}" class="button"> <input type="reset" value="{$lang->reset}" class="button">
                </form>
                <div style="display:block;">
                    <div id="perform_crm/salesreportlive_Results"></div>
                </div>
            </div>
        </td>
    </tr>
    {$footer}
</body>
</html>