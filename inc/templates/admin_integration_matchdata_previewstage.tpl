<h1>{$matchintegrationdata}</h1>
<form action="#" method="post" id="perform_integration/matchdata_Form" name="perform_integration/matchdata_Form">
    <input type="hidden" value="perform_matchdata" name="action" id="action" />
    <input type="hidden" value="{$core->input[matchitem]}" name="matchitem" id="matchitem" />
    <table width="100%" class="datatable">
        <thead>
        <th width="40%">{$lang->matchitem}</th><th>&nbsp;</th><th width="40%">{$lang->matchwith}</th>
        </thead>
        <tbody>
            {$integration_entries}
        </tbody>
    </table>
    <input type="button" class="button" value="{$lang->savecaps}" id="perform_integration/matchdata_Button"/>
    <div id="perform_integration/matchdata_Results"></div>
</form>