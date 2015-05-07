<div id="popup_cretefunction" title="{$lang->create}">
    <form action="#" method="post" id="perform_products/functions_Form" name="perform_products/functions_Form">
        <input type="hidden" name="action" value="do_create" />
        <input type="hidden" name="chemicalfunctions[cfid]" value="{$function->cfid}" />
        <input type="hidden" name="chemicalfunctions[name]" value="{$function->name}" />
        <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td width="40%"><strong>{$lang->name}</strong></td><td><input name="chemicalfunctions[title]" type="text" value="{$function->title}"/></td>
            </tr>
            <tr>
                <td><strong>{$lang->applications}</strong></td><td><select name="chemicalfunctions[segapplications][]" multiple="true">{$applications_list}</select></td>
            </tr>
            <tr>
                <td colspan="2"><strong>{$lang->description}</strong><br /><textarea rows="5" cols="30" id='description{$function->cfid}' class="basictxteditadv" name="chemicalfunctions[description]">{$function->description}</textarea></td>
            </tr>
            <tr>
                <td colspan="2" align="left">
                    <hr />
                    <input type='button' id='perform_products/functions_Button' value='{$lang->savecaps}' class='button'/>
                    <div id="perform_products/functions_Results"></div>
                </td>
            </tr>
    </form>
</div>