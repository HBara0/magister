<tr>
    <td> <a href="#ctid_{$task->ctid}" id="taskdetails_{$task->ctid}_calendar/taskboard_loadpopupbyid">{$task->subject} </a></td>
    <td>{$task->dueDate}</td>
    <td>{$task_icon[todo]}</td>
    <td> <span style="display:inline-block;">{$task_icon[inprogress]}</span> <span style="display:inline-block; vertical-align: top;"><small>{$task_percentage} </small> </span></td>
    <td>{$task_icon[completed]}</td>
</tr>