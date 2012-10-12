<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->generatereport}</title>
{$headerinc}
</head>
<body>
{$header}
<tr>
{$menu}
    <td class="contentContainer">
        <h3>{$lang->generatereport}</h3>
        <div style="margin-left: 5px;">
            <form name="do_attendance/generatereport_Form" id="do_attendance/generatereport_Form" method="post" action="index.php?module=attendance/generatereport&amp;action=do_generatereport">
                {$lang->employeename}</span><br />
                {$users_list}
                <p>
                    {$lang->fromdate} <br />
                    <input type="text" id="pickDate_from" autocomplete="off" tabindex="1" /> <input type="hidden" name="fromDate" id="altpickDate_from" />
                    <br />
                    {$lang->todate}<br />
                    <input type="text" id="pickDate_to" autocomplete="off" tabindex="2" /> <input type="hidden" name="toDate" id="altpickDate_to" /> 
                </p>
                <hr />
                <input type="submit" id="do_attendance/generatereport_Button" value="{$lang->generatereport}" class="button"> <input type="reset" value="{$lang->reset}" class="button">
            </form>
        </div>
    </td>
</tr>
{$footer}
</body>
</html>