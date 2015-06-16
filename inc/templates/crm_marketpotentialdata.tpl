<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->potentialmarketdata}</title>
        {$headerinc}
        <script src="{$core->settings[rootdir]}/js/profiles_marketintelligence.min.js" type="text/javascript"></script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>{$lang->potentialmarketdata}</h1>
            <form method='post' action='$_SERVER[REQUEST_URI]'>
                <div style="float: right">{$addmarketdata_link}</div>

                <table width="100%" border="0" cellspacing="0" cellpadding="2" class="datatable">
                    <thead>
                        <tr>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->affiliate}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->customer}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->country}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->product}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->supplier}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->chemicalsubs}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->basicingredients}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->functionalproperty}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->application}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->segment}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->brand}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->brandendproduct}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->endproducttype}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->characteristic}</th>
                            <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->potentialqty}</th>
                            <th width="150px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->marketshare}</th>
                            <th width="150px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->price}</th>
                            <th width="150px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->date}
                                <a href="{$sort_url}&amp;sortby=createdOn&amp;order=ASC">
                                    <img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/>
                                </a>
                                <a href="{$sort_url}&amp;sortby=createdOn&amp;order=DESC">
                                    <img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/>
                                </a></th>
                            <th>&nbsp;</th>
                        </tr><tr>
                            {$filters_row}
                        </tr>
                    </thead>
                    <tbody>
                        {$marketpotdata_list}
                    </tbody>
                </table>
            </form>
            <div style="width:40%; float:left; margin-top:0px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/></form></div>
        </td>
    </tr>
    {$footer}
    {$popup_createbrand}
    {$popup_marketdata}
</body>
</html>