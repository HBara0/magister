<h1>{$lang->manageevents}</h1>
<div>

    <form method="post" enctype="multipart/form-data" action="index.php?module=cms/manageevents&amp;action=do_perform_manageevents" target="uploadFrame">
        <input type='hidden' name='event[ceid]' value="{$event[ceid]}">
        <div style="display:block;">
            <div style="display: inline-block;width:10%">{$lang->title}</div>
            <div style="display: inline-block;padding:5px;"><input name="event[title]" type="text" value="{$event[title]}" required="required" size="100"><input type='hidden' name='event[alias]' value="{$event[alias]}"></div>
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
            <div style="display:inline-block; padding:11px;">{$lang->lang}</div>
            <div style="display:inline-block; padding:11px;">
                <select name="event[lang]">
                    <option value="english">{$lang->english}</option>
                    <option value="french">{$lang->french}</option>
                </select></div>
        </div>
        <div style="display:block;">
            <div style="display:inline-block;width:10%">{$lang->location}</div>
            <div style="display:inline-block;padding:5px;"><input name="event[place]" type="text" value="{$event[place]}" size="80"> {$lang->booth}Booth <input name="event[boothNum]" type="text" value="{$event[boothNum]}" size="10"></div>
        </div>
        <div>
            <input name="event[isPublic]" type='checkbox' value='1' checked='checked' style="display:none">
        </div>
        <div style="display:block;">
            <div style="display: inline-block;width:10%">{$lang->featureevents}</div>
            <div style="display: inline-block; padding:10px;"><input name="event[isFeatured]" type="checkbox" value="1" {$checkedbox[isFeatured]}></div>
        </div>
        <div style="display:block;" class="thead">{$lang->description}</div>
        <div style="display:block;"><textarea name="event[description]" cols="100" rows="6" id='description' class="txteditadv">{$event[description]}</textarea></div>
        <div style="display:block;" class="thead">{$lang->tags}</div>
        <div style="display:block;"><textarea name="event[tags]" cols="120" rows="2" id='tags'>{$event[tags]}</textarea><br /><span style="font-style: italic;">Seperate tags by comma. Tags help finding related items.</span><br /></div>
            {$restriction_selectlist}
            {$notifyevent_checkbox}
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
            <div style="display:block;" class="thead">{$lang->attacheventlogo}</div>
            <div style="display:block;">
                {$currentlogo}
                <input type='hidden' name='event[logo]' value='{$event[logo]}'>
                <fieldset class="altrow2" style="border:1px solid #DDDDDD">
                    <input type="file" id="logo" name="logo[]" multiple="true"></fieldset>
            </div>
        </div>
        <div style="display:block;padding-top:10px;">
            <div style="display:inline-block;">
                <input type="submit" value="{$lang->savecaps}" class="button" onclick="$('#upload_Result').show()"/>
                <input type="reset" class="button" value="{$lang->reset}"/>
                <div style="display:none" id="preview"><a id="preview_link" target="_blank" href="none"><button type="button" class="button">{$lang->preview}</button></a></div>
            </div>
        </div>
    </form>
    <hr />
    <iframe id='uploadFrame' name='uploadFrame' src='{$url}' frameBorder='0' width="100%" height="500px"> <div id="upload_Result" style="display:none;"><img src="{$core->settings[rootdir]}/images/loading.gif" /> {$lang->uploadinprogress}</div></iframe>
    <div id="perform_cms/manageevents_Results"></div>
</div>