<tr id="{$choicesrowid_rowid}" class="altrow2">
    <td style="{$showanswer}" id="answer_{$section_rowid}_{$question_rowid}_{$choicesrowid_rowid}"><input type="checkbox" value="1" {$choice_selected} name="section[{$section_rowid}][questions][{$question_rowid}][choices][{$choicesrowid_rowid}][isAnswer]"></td>
    <td><textarea name="section[{$section_rowid}][questions][{$question_rowid}][choices][{$choicesrowid_rowid}][choice]" title="{$lang->choices_tip}" cols="33" rows="3"></textarea></td>
    <td><textarea name="section[{$section_rowid}][questions][{$question_rowid}][choices][{$choicesrowid_rowid}][value]" title="{$lang->choices_tip}" cols="33" rows="3"></textarea></td>
</tr>