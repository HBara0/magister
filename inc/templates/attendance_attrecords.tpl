<h1>{$lang->attendancerecords}</h1>
<div style="float:right">{$addattendancelink}</div>
<form action="#" method="post" id="perform_attendance/attendancerecords_Form" name="perform_attendance/attendancerecords_Form" style="margin-bottom: 0px;">
    <table class="datatable" width="100%">
        <thead>
            <tr>
                <th>{$lang->employee} <a href="{$sort_url}&amp;sortby=uid&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=uid&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th>{$lang->date} <a href="{$sort_url}&amp;sortby=time&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=time&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                <th>{$lang->operation}</th>
                <th width="2%">&nbsp;</th>
            </tr>
            {$filters_row}
        </thead>
        <tbody>
            {$attendancelist}
        </tbody>
    </table>
</form>
<div style="margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist} <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
        {$addattendancerecord}
