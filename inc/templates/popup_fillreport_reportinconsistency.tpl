<div id="popup_reportinconsistency" title="{$lang->reportinconsistency}">
    <form name="perform_reporting/fillreport_Form" id="perform_reporting/fillreport_Form">
        <input type="hidden" name="action" value="do_reportinconsistency"/>
        <input type="hidden" name="productsactivity[paid]" value="{$paid}"/>

        <div>
            {$lang->comment}<br/><textarea name="productsactivity[comment]" class="basictxteditadv" id="inconsistencycomment_{$paid}"></textarea>
            <div>
                <input class="button" value="{$lang->report}" id="perform_reporting/fillreport_Button" type="submit">
                <div id="perform_reporting/fillreport_Results"></div>
            </div>
    </form>
</div>
