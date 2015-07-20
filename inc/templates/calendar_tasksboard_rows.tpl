<tr>
    <td><a href="#ctid_{$task->ctid}" id="taskdetails_{$task->ctid}_calendar/tasksboard_loadpopupbyid">{$task->subject}</a></td>
    <td>{$task->dueDate}</td>
    <td style='text-align: center;'>{$task_icon[pending]}</td>
    <td>{$progressbar}<div style="display: inline-block;width: 75px;height: 15px;text-align: center" id="progressbar{$task_barid}"><div style="width: 100%;height:15px;float: left;line-height: 120%" id='caption{$task_barid}'></div></td>
    <td style='text-align: center;'>{$task_icon[completed]}</td>
</tr>