<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->leavedetails}</title>
        {$headerinc}
        <style>
            .label{
                padding: 2px;
                display:inline-block;
                width: 20%;

            }
            .details{
                padding: 2px;
                display:inline-block;
                width: 75%;
            }
            .approved{
                color:rgb(102, 153, 0);
                display:inline-block;}
            .toapprove{
                display:inline-block;}

        </style>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->leavedetails}</h1>
            <div style="padding: 5px; margin-bottom:20px; word-wrap: break-word;">
                <div class="thead">{$lang->leavedetails}</div>
                <div class="label" style='vertical-align: top;'>{$lang->employeename}</div><div class="details">{$leave_obj->get_requester()->parse_link()}</div>

                <div class="label">{$lang->from}</div><div class="details">{$leave_obj->fromDate_output}</div>
                <div class="label">{$lang->to}</div><div class="details">{$leave_obj->toDate_output}</div>
                <div class="label">{$lang->actualworkingdays}</div><div class="details">{$workingdays}</div>
                <div class="label">{$lang->leavetype}</div><div class="details">{$leave_obj->get_type(false)->title}</div>
                <div class="label"></div><div class="details">{$additionalfield_output}</div>
                <div class="label">{$lang->leavereason}</div><div class="details">{$leave_obj->reason}</div>
                <div class="label">{$lang->approvals}</div>
                <div class="details">
                    <div class="approved">$approved</div>
                    <div class="toapprove">$toapprove</div>
                </div>

                <div class="thead" style="margin-top:15px;">{$lang->contactwhileabsent}</div>
                <div class="label" style='vertical-align:top;'>{$lang->leaveaddress}</div><div class="details">{$leave_obj->addressWhileAbsent}</div>
                <div class="label">{$lang->phonenumber}</div><div class="details">{$leave_obj->phoneWhileAbsent}</div>
                <div class="label">{$lang->contactpersonleave}</div><div class="details">{$contactperson}</div>
                <div class="label">{$lang->limitedemailaccess}</div><div class="details">{$limitedemail}</div>

                <div>{$conversation}</div>
                <div>$takeactionpage_conversation</div>



            </div>
        </td>
    </tr>
</html>