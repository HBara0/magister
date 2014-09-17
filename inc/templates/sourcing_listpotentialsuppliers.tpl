<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listpotentialsupplier}</title>
        {$headerinc}
        <link href="{$core->settings[rootdir]}/css/rateit.min.css" rel="stylesheet" type="text/css" />
        <script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
        <script>
            {$header_ratingjs}
        </script>
    </head><body>
        {$header}
    <tr> {$menu}
        <td class="contentContainer">
            <form action='$_SERVER[REQUEST_URI]' method="post">
                <div style="display:inline-block;"><h1>{$lang->listpotentialsupplier}</h1></div>
                <div style="display:inline-block;float:right; z-index:2;">  {$lang->chemicalsearch} <input id="filters_chemical" name="filters[chemicalsubstance]" type="text" size="35" onkeyup="$('#tablefilters').show();" />
                    <div style="display:inline-block;">{$lang->genericdproductsearch}</div><div style="display:inline-block;">{$genericproducts_selectlist}</div></div>
                <table class="datatable" width="100%">
                    <thead>
                        <tr>
                            <th width="19%">{$lang->companyname} <a href="{$sort_url}&amp;sortby=companyName&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=companyName&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th width="19%">{$lang->type}<a href="{$sort_url}&amp;sortby=type&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=type&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th width="19%">{$lang->segments}</th>
                            <th width="19%">{$lang->country} <a href="{$sort_url}&amp;sortby=country&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=country&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th width="19%">{$lang->opportunity} <a href="{$sort_url}&amp;sortby=businessPotential&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=businessPotential&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                            <th width="1%">&nbsp;</th>
                        </tr>

                        {$filters_row}

                    </thead>
                    <tbody>
                        {$sourcing_listpotentialsupplier_rows}
                    </tbody>

                </table>

            </form>
            <div style="width:40%; float:left; margin-top:0px;">
                <form method='post' action='$_SERVER[REQUEST_URI]'>
                    {$lang->perlist}:
                    <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
                </form>
            </div></td>
    </tr>
    {$footer}
    <div id="popup_requestchemical" title="{$lang->requestchemical}" >
        <form name='perform_sourcing/listpotentialsupplier_Form' id='perform_sourcing/listpotentialsupplier_Form' method="post">
            <input type="hidden" id="action" name="action" value="do_requestchemical" />
            <div style="display:table;">
                <div style="display:table-row">
                    <div style="display:table-cell; width:150px; vertical-align:middle;">{$lang->chemicalname}</div>
                    <div style="display:table-cell;">
                        <input type='text' id='chemicalproducts_1_autocomplete' autocomplete='off' size='40px'/>
                        <input type='hidden' id='chemicalproducts_1_id' name='request[product]'/>
                        <div id="searchQuickResults_chemicalproducts_1" class="searchQuickResults" style="display:none;"></div>
                    </div>
                </div>
                <div style="display:table-row;">
                    <div style="display:table-cell;width:150px; vertical-align:middle;">{$lang->application}</div>
                    <div style="display:table-cell;">
                        <select name="request[segmentapplication]" style="width:100%; max-width:90%;"><option></option>{$productsegment_applications}</select>
                    </div>
                </div>
                <div style="display:table-row;">
                    <div style="display:table-cell; width:150px; vertical-align:middle;">{$lang->origin}</div>
                    <div style="display:table-cell;">
                        {$origins_list}
                    </div>
                </div>
                <div style="display:table-row;">
                    <div style="display:table-cell; width:150px; vertical-align:middle;">{$lang->requestdescription}</div>
                    <div style="display:table-cell;">
                        <textarea name="request[requestDescription]" cols="40" rows="5" required='required'></textarea>
                    </div>
                </div>
                <hr />
                <div style="display:table-row">
                    <div style="display:table-cell">
                        <input type="button" id="perform_sourcing/listpotentialsupplier_Button" class="button main" value="{$lang->add}"/>
                    </div>
                </div>
            </div>
        </form>
        <div id="perform_sourcing/listpotentialsupplier_Results"></div>
    </div>
</body>
</html>