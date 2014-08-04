<div id="popup_blacklist"  title="{$lang->blacklsitsupplier}" >
    <form name='add_sourcing/managesupplier_Form' id='add_sourcing/managesupplier_Form' method="post">
        <input type="hidden" id="action" name="action" value="do_addblacklist" />
        <input type="hidden" id="action" name="supplier[blacklist][ssid]" value="{$supplier[details][ssid]}" />
        <div style="display:table-row">
            <div style="display:table-cell;  vertical-align:middle;">{$lang->daterequested}</div>
            <div style="display:table-cell"> <input type="text" id="pickDate_from" autocomplete="off" tabindex="1" value="{$supplier[details][blacklist][daterequested_output]}" required="required"/>
                <input type="hidden" name="supplier[blacklist][requestedOn]" id="altpickDate_from" value="{$supplier[details][blacklist][fromDate_formatted]}" /></div>
        </div>


        <div style="width:100%; height:250px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
            <table class="datatable" width="100%">
                <thead>
                <th>{$lang->chooserequester}</th>
                <th><input class='inlinefilterfield' type='text' style="width: 95%" placeholder="{$lang->employee}"/></th>
                </thead>
                <tbody>
                    {$users_rows}
                </tbody>
            </table>
        </div>


        <div style="display:table-row">
            <div style="display:table-cell; width:100px; vertical-align:middle;">{$lang->blacklistreason}</div>
            <div style="display:table-cell"><textarea name="supplier[blacklist][reason]" cols="40" required="required" rows="5">{$supplier[details][blacklist][reason]}</textarea></div>
        </div>


        <hr>
        <div style="display:table-row">
            <div style="display:table-cell">
                <input type="submit" id="add_sourcing/managesupplier_Button" class="button" value="{$lang->add}"/>
                <input type="reset" class="button" value="{$lang->reset}" />
            </div>
        </div>

    </form>
    <div id="add_sourcing/managesupplier_Results"></div>
</div>
