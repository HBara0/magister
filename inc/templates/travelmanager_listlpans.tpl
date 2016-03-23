<h3>{$lang->listplans}</h3>
<form name="perform_travelmanager/listplans_Form" id="perform_travelmanager/listplans_Form" action="#" method="post">
    <table class="datatable" width="100%">
        <table width="100%" class="datatable">
            <thead>
                <tr>
                    <th width="20%">{$lang->employee}</th>
                    <th width="30%">{$lang->title}</th>
                    <th width="15%">{$lang->fromdate}</th>
                    <th width="15%">{$lang->todate}</th>
                    <th width="15%">{$lang->createdon}
                        <a href="{$sort_url}&amp;sortby=createdOn&amp;order=ASC">
                            <img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/>
                        </a>
                        <a href="{$sort_url}&amp;sortby=createdOn&amp;order=DESC">
                            <img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/>
                        </a>
                    </th>
                    <th width="10%">{$lang->finalized}</th>
                    <th width="1%">&nbsp;</th>
                </tr>
                <tr>
                    {$filters_row}
                </tr>
            </thead>
            <tbody>
                {$plan_rows}
            </tbody>
        </table>
</form>
<div style="width:40%; float:left; margin-top:20px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
