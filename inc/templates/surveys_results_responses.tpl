<hr />
<div class="subtitle">{$lang->responses}</div>
<div style="width: 100%; height: 200px; overflow:auto; display: inline-block; vertical-align: top;">
    <table class="datatable">
        <thead>
            <tr class="thead">
                <th># <a href="{$sort_url}&amp;sortby=identifier&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=identifier&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th>{$lang->respondant} <a href="{$sort_url}&amp;sortby=respondant&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=respondant&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th>{$lang->responsetime} <a href="{$sort_url}&amp;sortby=time&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=time&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
            </tr>
        </thead>
        <tbody>
            {$responses_rows}
        </tbody>
    </table>
</div>