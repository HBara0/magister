<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->modifysitesettings}</title>
        {$headerinc}
        <script type="text/javascript">

        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->manageevents}</h1>
            <div>
                <iframe id='uploadFrame' name='uploadFrame' src='#'></iframe>
                <form method="post" enctype="multipart/form-data" action="index.php?module=cms/manageevents&amp;action=do_perform_manageevents" target="uploadFrame">
                    <div style="display:block;">
                        <div style="display: inline-block;width:10%">{$lang->title}</div>
                        <div style="display: inline-block;padding:5px;"><input name="event[title]" type="text" value="{$event[title]}" required="required" size="30"><input type='hidden' name='event[alias]' value="{$event[alias]}"></div>
                    </div>

                    <div style="display:block;">
                        <div style="display:inline-block;width:10%">{$lang->type}</div>
                        <div style="display: inline-block;padding:5px;">{$eventtypes_list}</div>
                    </div>
                    <div style="display:block;">
                        <div style="display:inline-block;width:10%">{$lang->fromdate}</div>
                        <div style="display: inline-block;padding:5px;">
                            <input type="text" name="event[fromDate]"  id="pickDate_eventfromdate" autocomplete="off" tabindex="2" value="{$event[fromDate_output]}" required='required' size="30"/>

        <!--   <input type="time" name="event[fromTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="{$current_date[hours]}:{$current_date[minutes]}" required="required">-->
                        </div>
                        <div style="display:inline-block;width:10%">{$lang->fromtime}</div>
                        <input type="time" name="event[fromTime]" value="{$event[fromTime_output]}"pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="{$current_date[hours]}:{$current_date[minutes]}">
                    </div>
                    <div>
                        <div style="width:10%; display:inline-block;">{$lang->todate}</div>
                        <div style="display: inline-block;padding:5px;">
                            <input type="text" name="event[toDate]" id="pickDate_eventtodate" autocomplete="off" tabindex="2" value="{$event[toDate_output]}" required='required' size="30"/>

                     <!-- <input type="time" name="event[toTime]" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="{$current_date[hours]}:{$current_date[minutes]}" required="required">-->
                        </div>
                        <div style="display:inline-block;width:10%">{$lang->totime}</div>
                        <input type="time" name="event[toTime]" value="{$event['toTime_output']}" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="{$current_date[hours]}:{$current_date[minutes]}">
                    </div>
                    <div style="display:block;">
                        <div style="display:inline-block;width:10%">{$lang->location}</div>
                        <div style="display:inline-block;padding:5px;"><input name="event[place]" type="text" value="{$event[place]}" size="30"></div>
                    </div>
                    <div>
                        <input name="event[isPublic]" type='checkbox' value='1' checked='checked' style="display:none">
                    </div>
                    <div style="display:block;">
                        <div style="display: inline-block;width:10%">{$lang->featureevents}</div>
                        <div style="display: inline-block; padding:10px;"><input name="event[isFeatured]" type="checkbox" value="1" {$checkedbox[isFeatured]}></div>
                    </div>
                    <div style="display:block;" class="thead">{$lang->description}</div>
                    <div style="display:block;"><textarea name="event[description]" cols="100"rows="6" id='description' class="txteditadv">{$event[description]}</textarea></div>
                        {$restriction_selectlist}
                        {$notifyevent_checkbox}

                    <div style="display:block;padding-top:5px;">
                        <div style="width:15%; display:inline-block;">{$lang->publishonwebsite}</div>
                        <div style="width:70%; display:inline-block;"><input name="event[publishOnWebsite]" type="checkbox" value="1" checked='checked'/></div>
                    </div>

                    <div style="display:block;padding-top:10px;width:100%;" class="thead">{$lang->inviteemployees}</div>
                    <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr>
                                    <th width="100%"><input type="checkbox" id='affiliatefilter_checkall'><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->employees}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
                                </tr>
                            </thead>
                            <tbody >
                                {$invitees_list}
                            </tbody>
                        </table>
                    </div>

                    <div style="display:block;padding-top:5px;">
                        <div style="display:block;" class="thead">{$lang->attachfiles}</div>
                        <div style="display:block;">
                            <fieldset class="altrow2" style="border:1px solid #DDDDDD">
                                <input type="file" id="attachments" name="event[attachments][]" multiple="true"></fieldset>
                        </div>

                    </div>

                    <div style="display:block;padding-top:5px;">
                        <div style="display:block;" class="thead">{$lang->attacheventlogo}</div>
                        <div style="display:block;">
                            <fieldset class="altrow2" style="border:1px solid #DDDDDD">
                                <input type="file" id="attachments" name="event[logo][]" multiple="true"></fieldset>
                        </div>

                    </div>

                    <div style="display:block;padding-top:10px;">
                        <div style="display:inline-block;">
                            <input type="submit" value="{$lang->savecaps}" class="button" onclick="$('#upload_Result').show()"/>
                            <input type="reset" class="button" value="{$lang->reset}"/>
                        </div>
                    </div>
                </form>
                <hr />
                <div id="upload_Result" style="display:none;"><img src="{$core->settings[rootdir]}/images/loading.gif" /> {$lang->uploadinprogress}</div>
                <div id="perform_cms/manageevents_Results"></div>

            </div>
        </td>
    </tr>
    {$footer}
</body>
</html>