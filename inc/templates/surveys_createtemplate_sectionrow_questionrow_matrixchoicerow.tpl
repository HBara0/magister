<tr id="{$matrixchoicesrowid_rowid}" class="altrow2">
    <td style="{$showanswer}" id="answer_{$section_rowid}_{$question_rowid}_{$matrixchoicesrowid_rowid}">
        <input type="checkbox" value="1" {$choice_selected} name="section[{$section_rowid}][questions][{$question_rowid}][matrixchoices][{$matrixchoicesrowid_rowid}][isAnswer]"></td>
    <td style="width:150px">
        <input type="text"  name="section[{$section_rowid}][questions][{$question_rowid}][matrixchoices][{$matrixchoicesrowid_rowid}][choice]" value="{$section[$section_rowid][questions][$question_rowid][matrixchoices][$matrixchoicesrowid_rowid][choice]}" size="75"/>

    </td>
</tr>