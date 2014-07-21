<tr>
    <td><a href="#ctid_{$task->ctid}" id="taskdetails_{$task->ctid}_calendar/tasksboard_loadpopupbyid">{$task->subject}</a></td>
    <td>{$task->dueDate}</td>
    <td style='text-align: center;'>{$task_icon[pending]}</td>
    <td style='text-align: center;'><div style="display:inline-block;">{$task_icon[inprogress]}</div> <div style="display:inline-block; vertical-align: top;" class="smalltext">{$task->percCompleted_output}</div></td>
    <td style='text-align: center;'>{$task_icon[completed]}</td>
</tr>