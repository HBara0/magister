<div class="container">
    <h1>{$lang->addassets}</h1>
    <form name="perform_assets/manageassets_Form" method="post" id="perform_assets/manageassets_Form" >
        <input type="hidden" name="asid" value="{$asid}" />
        <input type="hidden" value="do_{$actiontype}" name="action" id="action" />
        <div style="display:table; border-collapse:collapse; width:100%;">
            <div style="display:table-row;">
                <div style="display:table-cell; width:10%;">{$lang->tag}</div>
                <div style="display:table-cell; width:100%; padding:5px;"><input type="text" required="required" tabindex="1" id="asset[tag]" name="asset[tag]" value="{$assets[tag]}"/></div>
            </div>
            <div style="display:table-row;">
                <div style="display:table-cell; width:10%;">{$lang->title}</div>
                <div style="display:table-cell; width:100%; padding:5px;"><input type="text" required="required" tabindex="1" id="asset[title]" name="asset[title]" value="{$assets[title]}"/></div>
            </div>
            <div style="display:table-row;">
                <div style="display:table-cell; width:10%;">{$lang->affiliate}</div>
                <div style="display:table-cell; width:100%; padding:5px;"><select name="asset[affid]">{$affiliates_selectlist}</select></div>
            </div>
            <div style="display:table-row;">
                <div style="display:table-cell; width:10%;">{$lang->type}</div>
                <div style="display:table-cell; width:100%; padding:5px;">{$assettypes_selectlist}</div>
            </div>
            <div style="display:table-row;">
                <div style="display:table-cell; width:10%;">{$lang->status}</div>
                <div style="display:table-cell; width:100%;padding:5px;">{$assetsstatus_selectlist}</div>
            </div>
            <div style="display:table-row;">
                <div style="display:table-cell; width:10%; vertical-align:middle;">{$lang->description}</div>
                <div style="display:table-cell; width:100%;padding:5px;"><textarea  name="asset[description]" tabindex="4" cols="50" rows="8"/>{$assets[description]}</textarea></div>
            </div>
            <div style="display:table-row;">
                <div style="display: table-cell; width:20%;">
                    <input type="submit" class="button" value="{$lang->actiontype}" id="perform_assets/manageassets_Button" />
                    <input type="reset" class="button" value="{$lang->reset}"/>
                </div>
            </div>
        </div>
        <div style="display:table-row">
            <div style="display:table-cell;"id="perform_assets/manageassets_Results"></div>
        </div>
    </form>
</div>