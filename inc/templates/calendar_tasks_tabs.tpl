<script>
    $(function() {
        var tabs = $("#taskstabs").tabs();
    });
</script>
<h1>{$lang->tasksboard}</h1>
<div id="taskstabs"> <!--template-->
    <ul>
        <li><a href="#taskstabs-1" id="taskstabs-1_btn">{$lang->assignedtome}</a></li>
        <li><a href="#taskstabs-2" id="taskstabs_2_btn">{$lang->createdbyme}</a></li>
        <li><a href="#taskstabs-3" id="taskstabs-3_btn">{$lang->sharedwithme}</a></li>
    </ul>
    <div id="loadindsection"></div>
    <div id="taskstabs-1">{$calendar_taskboard_assigned}</div>
    <div id="taskstabs-2">{$calendar_taskboard_createdby}</div>
    <div id="taskstabs-3">{$calendar_taskboard_shared}</div>
</div>

{$helptour}
