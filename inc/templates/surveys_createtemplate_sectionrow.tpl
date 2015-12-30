<tr id="section_{$section_rowid}">
    <td style="border-bottom:2px solid #CCC; margin-bottom: 5px;">
        <table>
            <tr>
                <td style="padding:5px 5px 10px 5px;"><strong>{$lang->sectiontitle}</strong></td><td>
                    <input name="section[{$section_rowid}][title]" type="text" size="50" required="required" value="{$section[section_title]}">
                </td>
            </tr> <tr>
                <td style="padding:5px 5px 10px 5px;"><strong>{$lang->sectiondesciption}</strong></td><td>
                    <textarea cols="50" name="section[{$section_rowid}][description]" >{$section[section_description]}</textarea>
                </td>
            </tr>
        </table>
        <table width="100%">
            <tbody id="questions_{$section_rowid}_tbody"  class="{$altrow_class}">
                {$newquestions}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <img src="./images/add.gif" id="ajaxaddmore_surveys/createsurveytemplate_questions_{$section_rowid}" alt="{$lang->add}"><input id="numrows_questions_{$section_rowid}" name="numrows_questions{$section_rowid}" type="hidden" value="{$question_rowid}">
                        <input type="hidden" name="ajaxaddmoredata[type]" id="ajaxaddmoredata_questions_{$section_rowid}" value="{$type}"/>
                    </td>
                </tr>
            </tfoot>
        </table>
    </td>
</tr>