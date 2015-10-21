<td>{$lang->entity}</td>
<td colspan="2" style="display:block;">
    <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
        <table class="datatable" width="100%">
            <thead>
                <tr>
                    <th width="40%"><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->entities}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
                    <th width="20%"><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->affiliate}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
                    <th width="20%"><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->type}" style="display:inline-block;width:70%;margin-left:5px;"/></th>

                </tr>
            </thead>
            <tbody>
                {$ent_tobeassigned_list}
            </tbody>
        </table>
    </div>
    <div>
        <input type="button" value="{$lang->join}" class="button" id="joinentity"/>
    </div>
</td>