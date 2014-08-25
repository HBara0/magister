<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->leavedetails}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->leavedetails}</h1>
            <div style="padding: 5px; margin-bottom:20px; word-wrap: break-word;">
                <div class="thead">{$lang->leavedetails}</div>
                <div style='padding: 2px; display:inline-block; width: 30%;vertical-align: top;'>{$lang->employeename}</div><div style='padding: 2px; display:inline-block; width: 45%;'>{$leave_obj->user_output}</div>
                <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->from}</div><div  style=' padding: 2px; display:inline-block;width: 45%;'>{$leave_obj->fromDate_output}</div>
                <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->to}</div><div  style=' padding: 2px; display:inline-block;width: 45%;'>{$leave_obj->toDate_output}</div>
                <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->actualworkingdays}</div><div  style=' padding: 2px; display:inline-block;width: 45%;'>{$workingdays}</div>
                <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->leavetype}</div><div  style=' padding: 2px; display:inline-block;width: 45%;'>{$leave_obj->get_type(false)->title}</div>
                <div style='padding: 2px; display:inline-block; width: 30%;'></div><div  style=' padding: 2px; display:inline-block;width: 45%;'>{$additionalfield_output}</div>
                <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->leavereason}</div><div style=' padding: 2px; display:inline-block;width: 45%;'>{$leave_obj->reason}</div>
                <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->approvals}</div>
                <div style='padding: 2px; display:inline-block;width: 45%;'><span class="green_text">{$approved}</span><span>{$toapprove}</span></div>
                <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->affiliates}</div><div  style=' padding: 2px; display:inline-block;width: 45%;'>{$affiliates_list}</div>

                <div class="thead" style="margin-top:15px;">{$lang->contactwhileabsent}</div>
                <div style='padding: 2px; display:inline-block; width: 30%;vertical-align:top;'>{$lang->leaveaddress}</div><div style='padding: 2px; display:inline-block;width: 45%;'>{$leave_obj->addressWhileAbsent}</div>
                <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->phonenumber}</div><div style=' padding: 2px; display:inline-block;width: 45%;'>{$leave_obj->phoneWhileAbsent}</div>
                <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->contactpersonleave}</div><div  style=' padding: 2px; display:inline-block;width: 45%;'>{$contactperson}</div>
                <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->limitedemailaccess}</div><div  style=' padding: 2px; display:inline-block;width: 45%;'>{$limitedemail}</div>

                <div>{$conversation}</div>
                <div>$takeactionpage_conversation</div>
            </div>
        </td>
    </tr>
</html>