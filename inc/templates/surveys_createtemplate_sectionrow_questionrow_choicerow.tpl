<tr id="{$choicesrowid_rowid}" class="altrow2">
    <td style="{$showanswer}" id="answer_{$section_rowid}_{$question_rowid}_{$choicesrowid_rowid}"><input type="checkbox" value="1" {$choice_selected} name="section[{$section_rowid}][questions][{$question_rowid}][choices][{$choicesrowid_rowid}][isAnswer]"></td>
    <td style="width:150px"><input type="text"  name="section[{$section_rowid}][questions][{$question_rowid}][choices][{$choicesrowid_rowid}][choice]" size="75" /></td>
    <td style="float:left;"><input type="text" name="section[{$section_rowid}][questions][{$question_rowid}][choices][{$choicesrowid_rowid}][value]"></td>
</tr>