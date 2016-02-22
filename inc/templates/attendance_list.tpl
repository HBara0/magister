<div class="container">
    <h1>{$lang->attendancereport}</h1>
    <form action="#" method="post" id="perform_attendance/view_Form" name="perform_attendance/view_Form" style="margin-bottom: 0px;">
        <table class="datatable" width="100%">
            <thead>
                <tr>
                    <th>{$lang->employee} <a href="{$sort_url}&amp;sortby=username&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=username&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                    <th>{$lang->date} <a href="{$sort_url}&amp;sortby=date&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=date&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                    <th>{$lang->clockin}</th>
                    <th>{$lang->clockout}</th>
                    <th width="2%">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                {$attendancelist}
            </tbody>
        </table>
    </form>
    <div style="margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist} <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>

    <hr />

    <div style="width:100%; margin-top:4px; display:inline-block; text-align:right;" align="center" >
        <form name="perform_attendance/list_Form" id="perform_attendance/list_Form" method="post">
            <table align="left">
                <tr>
                    <td>
                        {$lang->fromdate}
                    </td>
                    <td>
                        <input type="text" id="pickDate_from" autocomplete="off" tabindex="1" />
                        <input type="hidden" name="fromDate" id="altpickDate_from" />
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align:top">{$lang->todate}</td>
                    <td>
                        <input type="text" id="pickDate_to" autocomplete="off" tabindex="2" />
                        <input type="hidden" name="toDate" id="altpickDate_to" />
                    </td>
                    <td>
                        <input type="button" id="perform_attendance/list_Button" value="{$lang->filter}" class="button">
                    </td>
                </tr>
            </table>
        </form>

        <form method='post' action='$_SERVER[REQUEST_URI]'>
            <table align="right">
                <tr>
                    <td>
                        <select id="filterby" name="filterby">
                            <option value="displayName">{$lang->displayname}</option>
                            <option value="firstName">{$lang->firstname}</option>
                        </select>
                        <input type="text" name="filtervalue" id="filtervalue">
                        <input type="submit" class="button" value="{$lang->filter}">
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div id="perform_attendance/list_Results"></div>
</div>