<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->leavedetails}</title>
        {$headerinc}
        <style>
            .lefttext{
                padding: 2px;
                display:inline-block;
                width: 20%;
                font-weight: bold;
            }
            .righttext{
                padding: 2px;
                display:inline-block;
                width: 75%;}
            </style>
        </head>
        <body>
            {$header}
        <tr>
            {$menu}
            <td class="contentContainer">
            <h1>{$lang->leavedetails}</h1>
            <div style="padding: 5px; margin-bottom:20px; word-wrap: break-word;">
                <div class="subtitle">{$lang->leavedetails}</div>
                <div class="lefttext" style='vertical-align: top;'>{$lang->employeename}</div><div class="righttext">{$leave_obj->get_requester()->displayName}</div>

                <div class="lefttext">{$lang->from}</div><div class="righttext">{$leave_obj->fromDate}</div>
                <div class="lefttext">{$lang->to}</div><div class="righttext">{$leave_obj->toDate}</div>
                <div class="lefttext">{$lang->leavetype}</div><div class="righttext">{$leave_obj->get_type(false)->name}</div>
                <div class="lefttext">{$lang->leavereason}</div><div class="righttext">{$leave_obj->reason}</div>

                <div class="subtitle">{$lang->contactwhileabsent}</div>
                <div class="lefttext" style='vertical-align:top;'>{$lang->leaveaddress}</div><div class="righttext">{$leave_obj->addressWhileAbsent}</div>
                <div class="lefttext">{$lang->phonenumber}</div><div class="righttext">{$leave_obj->phoneWhileAbsent}</div>
                <div class="lefttext">{$lang->contactpersonleave}</div><div class="righttext">{$leave_obj->contactPerson}</div>
                <div class="lefttext">{$lang->limitedemailaccess}</div><div class="righttext">{$leave_obj->limitedEmail}</div>
                <div class="lefttext">{$lang->approvedby}</div><div class="righttext">{}</div>

                <div class="subtitle">{$lang->additionaldetails}</div>
                <div>$var</div>



            </div>
        </td>
    </tr>
</html>