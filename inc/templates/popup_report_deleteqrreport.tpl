<div id="popup_deleteqrreport" title="{$lang->deletereport}">
    <form id="perform_reporting/list_Form" name="perform_reporting/list_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_deleteqr" />
        <input type="hidden" id="todelete" name="delete[rid]" value="{$id}" />

        <div class="form-group">
            <label for="user_1_autocomplete">{$lang->requestedby}*</label>
            <input  class="form-control" type="text" id="user_1_autocomplete" autocomplete="false" tabindex="1"  required="required"/>
            <input type='hidden' id='userInterface_1_id'  name="delete[uid]" disabled/>
            <input type='hidden' id='user_1_id_output' name="delete[uid]" />
        </div>
        <div data-toggle="tooltip" data-placement="top" title="{$lang->referenceexplanation}" class="form-group">
            <label for="reference" >{$lang->reference}*</label>
            <input type='text' id='reference' class="form-control"  name="delete[reference]" />
        </div>
        <div align="center">
            <hr />
            <input type='button' id='perform_reporting/list_Button' value='{$lang->submit}' class='button'/>
        </div>
        <div style="display:table-row">
            <div style="display:table-cell;" id="perform_reporting/list_Results"></div>
        </div>
    </form>
</div>