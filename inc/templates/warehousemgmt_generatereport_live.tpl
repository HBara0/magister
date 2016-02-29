<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->stockreport}</title>
        {$headerinc}
        <script type="text/javascript" src="{$core->settings[rootdir]}/js/tableExport.min.js"></script>
        <script type="text/javascript" src="{$core->settings[rootdir]}/js/jquery.base64.min.js"></script>
        <script type="text/javascript">
            $(function() {
                $(document).on('change', "select[id='type']", function() {
                    var value = $("select[id='type'] option:selected").attr("value");
                    if(value == 'endofmonth') {
                        $("div[id='asof']").hide();
                    } else {
                        $("div[id='asof']").show();
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
            <h1>{$lang->stockreport}</h1>
            <div style="margin-left: 5px;">
                <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p>This report reads data from Openbravo. If you find any wrong info, please verify it on Openbravo.</p></div>

                <form name="do_stock/salesreport_Form" id="do_warehousemgmt/stockreportlive_Form" method="post" action="index.php?module=warehousemgmt/stockreportlive&amp;action=do_generatereport">
                    <div style="vertical-align:top;"><strong>{$lang->affiliate}</strong><br />{$affiliates_list}</div>
                    <div style="vertical-align:top;">{$lang->type}<br />
                        <select name="type" id="type">
                            <option value=""></option>
                            <option value="endofmonth">{$lang->endofmonth}</option>
                        </select>
                    </div>
                    <div style="vertical-align:top;" id="asof">{$lang->asof}<br />
                        <input type="text" id="pickDate_asOf" autocomplete="off" tabindex="1" /> <input type="hidden" name="asOf" id="altpickDate_asOf" /> <img src="images/icons/question.gif" title="If left empty, system assumes that report is as of today.">
                    </div>
                    <hr />
                    <input type="submit" id="do_warehousemgmt/stockreportlive_Button" value="{$lang->generatereport}" class="button"> <input type="reset" value="{$lang->reset}" class="button">
                </form>
            </div>
        </td>
    </tr>
    {$footer}
</body>
</html>