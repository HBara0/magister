<div id="popup_creteapplication"  title="{$lang->create}">
    <form action="#" method="post" id="perform_products/applications_Form" name="perform_products/applications_Form">
        <input type="hidden" name="action" value="do_create" />
        <input type="hidden" name='segmentapplications[psaid]' value='{$application->psaid}'/>
        <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td width="40%"><strong>{$lang->name}</strong></td><td> <input name="segmentapplications[title]" value='{$application->title}' type="text"/></td>
            </tr>
            <tr>
                <td width="40%"><strong>{$lang->segment}</strong></td><td>{$segments_list}</td>
            </tr>
            <tr>
                <td width="40%"><strong>{$lang->sequence}</strong></td><td><input name="segmentapplications[sequence]" value='{$application->sequence}' min="0" type="number"/></td>
            </tr>
            <tr>
                <td colspan="2">{$lang->description}<br /><textarea name="segmentapplications[description]" id='description{$application->psaid}' cols="50" class="basictxteditadv" rows="25">{$application->description}</textarea></td>
            </tr>
            <tr>
                <td>{$lang->publishonweb}</td><td>{$publishonwebcheckbox}</td>
            </tr>
            <tr><td colspan="2"><hr /></td></tr>
            <tr>
                <td><strong>{$lang->functions}</strong></td><td><select name="segmentapplications[segappfunctions][]" multiple="true">{$checmicalfunctions_list}</select></td>
            </tr>
            <tr>
                <td colspan="2" align="left">
                    <hr />
                    <input type='button' id='perform_products/applications_Button' value='{$lang->savecaps}' class='button'/>
                    <div id="perform_products/applications_Results"></div>
                </td>
            </tr>
        </table>
    </form>
</div>