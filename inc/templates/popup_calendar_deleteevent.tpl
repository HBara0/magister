<div id="popup_deleteevent" title="{$lang->deleteevent}">
    <form id="perform_calendar/manageevents_Form" name="perform_calendar/manageevents_Form" action="#" method="post">
        <input type="hidden" name="action" value="delete_event" />
        <input type="hidden"  name="id" value="{$event['ceid']}" />
        <strong>{$lang->sureredeleteevent}</strong>
        <hr />
        <div align="center"><input type='button' id='perform_calendar/manageevents_Button' value='{$lang->yes}' class='button'/></div>
    </form>
    <div id="perform_calendar/manageevents_Results"></div>
</div>