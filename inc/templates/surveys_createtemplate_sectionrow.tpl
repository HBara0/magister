<tr id="section_{$section_rowid}" class="altrow2">
    <td width="20%"><div><strong>{$lang->sectiontitle}</strong>
        <input name="section[{$section_rowid}][title]" type="text"></div>
        <table width="100%" class="{$altrow_class}">
            <tbody  id="questions{$section_rowid}_tbody">
                {$newquestions}
            </tbody>  
        <tfoot>
            <tr>
                <td height="38" colspan="2">
                    <img src="./images/add.gif" id="ajaxaddmore_surveys/createsurveytemplate_questions_{$section_rowid}" alt="{$lang->add}"><input id="numrows_questions{$section_rowid}" name="numrows_questions{$section_rowid}" type="hidden" value="{$question_rowid}">
                </td>
            </tr> 
        </tfoot>
        </table>
    </td>
</tr>