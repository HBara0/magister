<td class="contentContainer">
    <h3>{$lang->viewplan}: {$plan_name}</h3>
    <div id="container" style="width:100%; margin: 0px auto; display:block;">
        {$leave_details}
        <div style=" width:100%; background-color:white ; display: block;">{$segment_expenses}</div>
        {$segment_details}
        <br/>
        {$transportaion_fields}
        <form name="perform_travelmanager/viewplan_Form" id="perform_travelmanager/viewplan_Form" action="#" method="post">
            <input type="hidden" name="planid" value="{$planid}"/>
            <div {$display_fin}>
                {$checkbox['confirm']}
                {$finalize_button}
            </div>
            <button {$hide_close} id='closepage'>Close</button>
        </form>
        <div id="perform_travelmanager/viewplan_Results"></div>

</td>