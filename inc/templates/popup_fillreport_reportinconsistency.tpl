<div id="popup_reportinconsistency" title="{$lang->reportinconsistency}">
    <form name="perform_reporting/fillreport_Form" id="perform_reporting/fillreport_Form">
        <div class="ui-state-highlight ui-corner-all"><em>{$lang->reportinginconsistencypleaseaddcomment}</em></div>
        <input type="hidden" name="action" value="do_reportinconsistency"/>
        <input type="hidden" name="productsactivity[paid]" value="{$paid}"/>
        <div>Report Details</div>
        <div><h4 class="title">{$reportyear}/{$affiliatename}</h4></div>
        <div><h4 class="title">{$product}</h4></div>
        <div>
            {$lang->comment}<br/><textarea name="productsactivity[comment]" class="basictxteditadv" id="inconsistencycomment_{$paid}"></textarea>
            <div>
                <input class="button" value="{$lang->report}" id="perform_reporting/fillreport_Button" type="submit">
                <div id="perform_reporting/fillreport_Results"></div>
            </div>
    </form>
</div>
