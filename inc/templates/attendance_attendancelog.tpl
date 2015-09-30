<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->attendancelog}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->attendancelog}</h1>
            <div style="margin-left: 5px;">
                <form name="do_attendance/generatereport_Form" id="do_attendance/generatereport_Form" method="post" action="index.php?module=attendance/generatereport&amp;action=do_generatereport">
                    </span><br />
                    <input type="hidden" name="referrer" value="log">
                    <div style="width:100%; height:250px; overflow:auto; vertical-align:top; margin-bottom: 10px;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr>
                                    <th width="50%"><input style="width:5%;" type="checkbox" id='usersfilter_checkall'><input class='inlinefilterfield' type='text'  placeholder="{$lang->search}    {$lang->employeename}" style="width:90%;display:inline-block;margin-left:5px;"/></th>
                                    <th width="40%"> <input class='inlinefilterfield' type='text'  placeholder="{$lang->search} {$lang->affiliates}" style="width:100%;display:inline-block;margin-left:5px;"/></th>
                                </tr>
                            </thead>
                            <tbody>
                                {$users_list}
                            </tbody>
                        </table>

                    </div>
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