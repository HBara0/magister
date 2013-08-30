<div id="popup_editasset" title="{$lang->editasset}">
    <form name="perform_assets/manageassets_Form" method="post" id="perform_assets/manageassets_Form" >
        <input type="hidden" name="asid" value="{$core->input[id]}" />
        <input type="hidden" value="do_{$actiontype}" name="action" id="action" />
        <div style="display:table; border-collapse:collapse; width:100%;">

            <div style="display:table-row;">
                <div style="display:table-cell; width:10%;">{$lang->tag}</div>
                <div style="display:table-cell; width:100%; padding:5px;"><input type="text" required="required" tabindex="1" id="asset[tag]" name="asset[tag]" value="{$asset[tag]}"/></div>
            </div>
            <div style="display:table-row;">
                <div style="display:table-cell; width:10%;">{$lang->title}</div>
                <div style="display:table-cell; width:100%; padding:5px;"><input type="text" required="required" tabindex="1" id="asset[title]" name="asset[title]" value="{$asset[title]}"/></div>
            </div>

            <div style="display:table-row;">
                <div style="display:table-cell; width:10%;">{$lang->affiliate}</div>
                <div style="display:table-cell; width:100%; padding:5px;"><select name="asset[affid]">{$affiliates_list}</select></div>
            </div>

            <div style="display:table-row;">
                <div style="display:table-cell; width:10%;">{$lang->type}</div>
                <div style="display:table-cell; width:100%; padding:5px;">{$assettypes_selectlist}</div>
            </div>


            <div style="display:table-row;">
                <div style="display:table-cell; width:10%;">{$lang->status}</div>
                <div style="display:table-cell; width:100%;padding:5px;">{$assetstatus_selectlist}</div>
            </div>


            <div style="display:table-row;">
                <div style="display:table-cell; width:10%; vertical-align:middle;">{$lang->description}</div>
                <div style="display:table-cell; width:100%;padding:5px;"><textarea  name="asset[description]" tabindex="4" cols="50" rows="5">{$asset[description]}</textarea></div>
            </div>
            <div style="display:table-row;">
                <div style="display:table-cell; width:10%; vertical-align:middle;">{$lang->isactive}</div>
                <div style="display:table-cell; width:100%;padding:5px;"><input type="checkbox" name="asset[isActive]" value="1"{$checkboxes[isActive]}/></div>
            </div>




            <div style="display:table-row;">
                <div style="display: table-cell; width:30%;">
                    <input type="submit" class="button" value="{$lang->$actiontype}" id="perform_assets/manageassets_Button" />
                    <input type="reset" class="button"   onclick="$('#popup_editasset').dialog('close')"value="{$lang->close}"/>
                </div>
            </div>
        </div>
        <div style="display:table-row">
            <div style="display:table-cell;"id="perform_assets/manageassets_Results"></div>
        </div>
    </form>


</div>