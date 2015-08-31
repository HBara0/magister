
<div class="subtitle">{$lang->pendingresponses}</div>

<div style="width: 100%; height: 200px; overflow:auto; display: inline-block; vertical-align: top;">
    <table class="datatable">
        <thead>
            <tr class="thead">
                <th># <a href="{$sort_url}&amp;sortby=identifier&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=identifier&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                <th>{$lang->invitee} <a href="{$sort_url}&amp;sortby=invitee&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=employee&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
            </tr>
        </thead>

        <tbody>
            {$pendingresponsesrows}
        </tbody>
    </table>
</div>