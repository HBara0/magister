<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$pagetitle}</title>
        {$headerinc}
    </head>
    <body>
        {$header}

        <script>
            $(function () {
                $(document).on('click', 'a[id^=deletefile_]', function () {
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }
                    var id = $(this).attr('id').split("_");
                    sharedFunctions.requestAjax("post", "index.php?module=meetings/create&action=deletefile", "mattid=" + id[1], 'deletecontainer_' + id[1], 'deletecontainer_' + id[1], true);

                });

            });
        </script>
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$pagetitle}</h1>
            <iframe id='uploadFrame'  name='uploadFrame' style="display:none;" ></iframe>
            <form method="post" enctype="multipart/form-data" action="index.php?module=meetings/create" target="uploadFrame">

                <input type="hidden" value="do_{$action}meeting" name="action" id="action" />
                <input type="hidden" value="{$core->input[mtid]}" name="mtid"  />
                <table cellpadding="1" cellspacing="1" width="100%">
                    <tr>
                        <td>{$lang->title}</td>
                        <td><input type="text" tabindex="1" name="meeting[title]" id="meeting_title" size="100" required="required" value="{$meeting[title]}"/></td>
                    </tr>
                    <tr><td class="subtitle" colspan="2" id="createmeeting_associations_title">{$lang->associations}</td></tr>
                        {$createmeeting_associations}
                    <tr>
                        <td>{$lang->fromdate}</td>
                        <td><input type="text" tabindex="2" id="pickDate_from" autocomplete="off" tabindex="1"  name="meeting[fromDate]" value="{$meeting[fromDate_output]}" required="required"/>
                            <input type="hidden" name="meeting[altfromDate]" id="altpickDate_from" value="{$meeting[fromDate]}" /> <input type="time" tabindex="3" name="meeting[fromTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="08:00" value="{$meeting[fromTime_output]}" required="required"></td>
                    </tr>
                    <tr>
                        <td>{$lang->todate}</td>
                        <td><input type="text" tabindex="4" id="pickDate_to" autocomplete="off" tabindex="1" name="meeting[toDate]"  value="{$meeting[toDate_output]}" required="required"/><input type="hidden" name="meeting[alttoDate]" id="altpickDate_to" value="{$meeting[toDate]}" /> <input type="time" name="meeting[toTime]" tabindex="5" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="17:00" value="{$meeting[toTime_output]}" required="required"></td>
                    </tr>

                    <tr>
                        <td>{$lang->description}</td>
                        <td><textarea class="txteditadv" tabindex="6" id="description" name="meeting[description]" cols="20" rows="5">{$meeting[description]}</textarea> </td>
                    </tr>
                    <tr>
                        <td>{$lang->location}</td>
                        <td><input type="text" name="meeting[location]" size="60" tabindex="7" value="{$meeting[location]}" id="meeting_location"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->ispublic}</td>
                        <td><input type="checkbox" name="meeting[isPublic]" tabindex="8" value="1"{$checked_checkboxes[isPublic]} id="meeting_ispublic"/></td>
                    </tr>
                    <tr>
                        <td colspan="2" id="intivationssection">
                            <table border="0" width="100%" cellspacing="1" cellpadding="1">
                                <tr><td class="thead" colspan="3">{$lang->attendees}</td></tr>
                                <tr>
                                    <td><input type="checkbox" id='notify_user' name='meeting[notifyuser]' value="1" {$disabled_checkboxes['notifyuser']}> {$lang->notifyusers}</td>
                                    <td><input type="checkbox"{$checked} id='notify_representative' name='meeting[notifyrep]' value="1" {$disabled_checkboxes['notifyrep']}> {$lang->notifyreps}</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div style="display: inline-block;">
                                            <table border="0" width="50%" cellspacing="1" cellpadding="1">
                                                <tbody id="attendees_tbody">
                                                    {$createmeeting_userattendees}
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                    <td style="vertical-align: top;">
                                        <div style="display: inline-block;">
                                            <table border="0" width="50%" cellspacing="1" cellpadding="1">
                                                <tbody id="rep_tbody">
                                                    {$createmeeting_repattendees}
                                                </tbody>
                                            </table>
                                        </div></td>
                                </tr>
                                <tr><td><img src="images/add.gif" id="addmore_attendees" alt='{$lang->add}'> {$lang->add}</td><td><img src="images/add.gif" id="addmore_rep" alt='{$lang->add}' title="{$lang->addmorerows}"> {$lang->add}</td></tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr><td colspan="2" class="subtitle">{$lang->attachements}</td></tr>
                        {$meeting_attachments}
                    <tr>
                        <td colspan="2">

                            <input type="submit" class="button main" value="{$lang->savecaps}" id="meetings_create" onclick="$('#upload_Result').show()"  />
                            <input type="reset" class="button" value="{$lang->reset}"/>
                            <hr />
                            <div id="upload_Result" style="display:none;"><img src="{$core->settings[rootdir]}/images/loading.gif" /> {$lang->uploadinprogress}</div>
                        </td>
                    </tr>
                </table>
            </form>
            {$helptour}
        </td>
    </tr>
    {$createmeeting_deletefile}
    {$footer}
</body>
</html>