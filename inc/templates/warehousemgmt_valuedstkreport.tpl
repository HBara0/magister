<h1>{$lang->valuedstockreport}</h1>
<div style="margin-left: 5px;">
    <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p>This report reads data from Openbravo. If you find any wrong info, please verify it on Openbravo.</p></div>

    <form name="perform_warehousemgmt/valuedstockreport_Form" id="perform_warehousemgmt/valuedstockreport_Form" method="post">
        <div style="vertical-align:top;"><strong>{$lang->affiliate}</strong><br />{$affiliates_list}</div>
        <div style="vertical-align:top;">{$lang->asof}<br />
            <input type="text" id="pickDate_asOf" autocomplete="off" tabindex="1" /> <input type="hidden" name="asOf" id="altpickDate_asOf" /> <img src="images/icons/question.gif" title="If left empty, system assumes that report is as of today.">
        </div>
        <hr />
        <input type="submit" id="perform_warehousemgmt/valuedstockreport_Button" value="{$lang->generatereport}" class="button"> <input type="reset" value="{$lang->reset}" class="button">
    </form>
</div>
<div id="perform_warehousemgmt/valuedstockreport_Results"></div>
