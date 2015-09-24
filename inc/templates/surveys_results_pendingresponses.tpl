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
<div style="display:inline-block; width: 25%; margin:0; text-align:right; float:right;">
    <div id="perform_surveys/viewresults_Results">
        <form action="#" method="post" id="perform_surveys/viewresults_Form" name="perform_surveys/viewresults_Form">
            <input type="hidden" value="{$core->input[identifier]}" name="identifier">
            <input name="action" value="sendreminders" type="hidden" />
            <input value="{$lang->sendreminders}" type="button" id="perform_surveys/viewresults_Button" class="button" {$display[sendreminders]}/>
        </form>
    </div>
</div>