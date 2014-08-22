<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->leavedetails}</title>
        {$headerinc}
        <style>
            .lefttext{
                padding: 2px;
                display:inline-block;
                width: 20%;

            }
            .righttext{
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
                <div class="lefttext" style='vertical-align: top;'>{$lang->employeename}</div><div class="righttext">{$leave_obj->get_requester()->displayName}</div>

                <div class="lefttext">{$lang->from}</div><div class="righttext">{$leave_obj->fromDate_output}</div>
                <div class="lefttext">{$lang->to}</div><div class="righttext">{$leave_obj->toDate_output}</div>
                <div class="lefttext">{$lang->actualworkingdays}</div><div class="righttext">{$workingdays}</div>
                <div class="lefttext">{$lang->leavetype}</div><div class="righttext">{$leave_obj->get_type(false)->title}</div>
                <div class="lefttext"></div><div class="righttext">{$additionalfield_output}</div>
                <div class="lefttext">{$lang->leavereason}</div><div class="righttext">{$leave_obj->reason}</div>
                <div class="lefttext">{$lang->approvals}</div>
                <div class="righttext">
                    <div class="approved">$approved</div>
                    <div class="toapprove">$toapprove</div>
                </div>

                <div class="thead" style="margin-top:15px;">{$lang->contactwhileabsent}</div>
                <div class="lefttext" style='vertical-align:top;'>{$lang->leaveaddress}</div><div class="righttext">{$leave_obj->addressWhileAbsent}</div>
                <div class="lefttext">{$lang->phonenumber}</div><div class="righttext">{$leave_obj->phoneWhileAbsent}</div>
                <div class="lefttext">{$lang->contactpersonleave}</div><div class="righttext">{$contactperson}</div>
                <div class="lefttext">{$lang->limitedemailaccess}</div><div class="righttext">{$limitedemail}</div>

                <div>{$conversation}</div>
                <div>$takeactionpage_conversation</div>



            </div>
        </td>
    </tr>
</html>