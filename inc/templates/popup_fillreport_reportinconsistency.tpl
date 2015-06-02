<div id="popup_reportinconsistency" title="{$lang->reportinconsistency}">
    <form name="perform_reporting/fillreport_Form" id="perform_reporting/fillreport_Form">
        <input type="hidden" name="action" value="do_reportinconsistency"/>
        <input type="hidden" name="productsactivity[paid]" value="{$paid}"/>

        <div>
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr><td>{$lang->comment}</td><td><textarea name="productsactivity[comment]" class="basictxteditadv"></textarea> </td></tr>
            </table>
            <div>
                <input class="button" value="{$lang->report}" id="perform_reporting/fillreport_Button" type="submit">
                <div id="perform_reporting/fillreport_Results"></div>
            </div>
    </form>
</div>
