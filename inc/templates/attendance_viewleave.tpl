<h1>{$lang->leavedetails}</h1>
<div style="padding: 5px; margin-bottom:20px; word-wrap: break-word; vertical-align: top;">
    <div style='padding: 2px; display:inline-block; width: 30%;vertical-align: top;'>{$lang->employeename}</div><div style='padding: 2px; display:inline-block; width: 45%;'>{$leave->user_output}</div>
    <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->fromdate}</div><div style=' padding: 2px; display:inline-block;width: 45%;'>{$leave->fromDate_output}</div>
    <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->todate}</div><div style=' padding: 2px; display:inline-block;width: 45%;'>{$leave->toDate_output}</div>
    <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->actualworkingdays}</div><div  style=' padding: 2px; display:inline-block;width: 45%;'>{$workingdays}</div>
    <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->leavetype}</div><div style=' padding: 2px; display:inline-block;width: 45%;'>{$leave->get_type(false)->title}</div>
    <div style='padding: 2px; display:inline-block; width: 30%;'></div><div  style='padding: 2px; display:inline-block; width: 45%;'>{$additionalfield_output}</div>
    <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->leavereason}</div><div style='padding: 2px; display:inline-block;width: 45%;'>{$leave->reason}</div>
    <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->approvals}</div>
    <div style='padding: 2px; display:inline-block; width: 45%;'><span class="green_text">{$approved}</span> <span>{$toapprove}</span></div>
    <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->informedaffiliates}</div><div style=' padding: 2px; display:inline-block;width: 60%;'>{$affiliates_list}</div>
    <br />
    <br />
    <h2>{$lang->contactwhileabsent}</h2>
    <div style='padding: 2px; display:inline-block; width: 30%;vertical-align:top;'>{$lang->leaveaddress}</div><div style='padding: 2px; display:inline-block;width: 45%;'>{$leave->addressWhileAbsent}</div>
    <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->phonenumber}</div><div style=' padding: 2px; display:inline-block;width: 45%;'>{$leave->phoneWhileAbsent}</div>
    <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->contactpersonleave}</div><div style=' padding: 2px; display:inline-block;width: 45%;'>{$contactperson}</div>
    <div style='padding: 2px; display:inline-block; width: 30%;'>{$lang->limitedemailaccess}</div><div style=' padding: 2px; display:inline-block;width: 45%;'>{$limitedemail}</div>
    <br />
    <br />
    <h2 style="margin-top:15px;">{$lang->conversation}</h2>
    <div>{$conversation}</div>
</div>
