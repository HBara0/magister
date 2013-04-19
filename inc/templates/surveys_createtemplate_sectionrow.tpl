<tr id="section_{$section_rowid}">
    <td width="20%" style="border-bottom:2px solid #CCC; margin-bottom: 5px;">
        <div style="padding:5px 5px 10px 5px;"><strong>{$lang->sectiontitle}</strong>
            <input name="section[{$section_rowid}][title]" type="text" size="50"></div>
        <table width="100%">
            <tbody  id="questions{$section_rowid}_tbody"  class="{$altrow_class}">
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