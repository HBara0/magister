
<td colspan="3" >
    <table class="datatable" width="100%">
        <thead>
            <tr>
                <th class="thead" colspan="3">{$lang->joinentities}
                </th>
            </tr>
            <tr>
                <th colspan="3">
        <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;">
            <p>{$lang->alreadyassigned}</p>
        </div>
    </th>
</tr>
<tr>
    <th width="40%"><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->entity}" style="display:inline-block;width:100%;margin-left:5px;"/></th>
    <th width="20%"><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->type}" style="display:inline-block;width:100%;margin-left:5px;"/></th>
    <th width="40%"><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->affiliate}" style="display:inline-block;width:100%;margin-left:5px;"/></th>
</tr>
</thead>
<tbody>
    {$ent_tobeassigned_list}
</tbody>
<tfoot>
<th colspan='2'></th>
<th>    <input type="button" value="{$lang->join}" class="button" id="joinentity" style='float:right'/>
</th>
</tfoot>
</table>

</td>