<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->managebrands}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->managebrands}</h3>
            <table class="datatable">
                <div style="float:right;" class="subtitle"> <a href="#" id="showpopup_createbrand" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->createbrand}</a></div>
                <thead>
                    <tr>   
                        <th>{$lang->name} <a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                        <th>{$lang->supplier} </th>
                    </tr>
                </thead>
                <tbody>
                    {$entitybrands_list}
                </tbody>
                <tr>
                    <td>
                        <div style="width:40%; float:left; margin-top:0px;">
                            <form method='post' action='$_SERVER[REQUEST_URI]'>
                                {$lang->perlist}:
                                <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
                            </form>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <div id="popup_createbrand" title="{$lang->createbrand}">
        <form action="#" method="post" id="perform_entities/managebrands_Form" name="perform_entities/managebrands_Form">
            <input type="hidden" name="action" value="do_create" />
            <div style="display:block;">
                <div style="display:inline-block;padding:8px;"><strong>{$lang->name}</strong></div>
                <div style="display:inline-block;"> 
                    <input name="entitybrand[title]" type="text"/>
                </div>      
            </div>  
            <div style="display:block;">
                <div style="display: inline-block;padding:8px;"><strong>{$lang->customer}</strong></div>
                <div style="display: inline-block;"><input type='text' id='customer_1_QSearch'/>
                    <input type="text" size="3" id="customer_1_id_output" value="{$entitybrand[spid]}" disabled/>
                    <input type='hidden' id='customer_1_id' name='entitybrand[eid]' value="{$entitybrand[eid]}" />
                    <div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div> </div>
            </div>
            <div style="display:table;">
                <div style="display:table-row;">
                    <div style="display:table-cell;padding:8px;">{$lang->endproducttypes} </div>
                      <div style="display:table-cell;padding:8px;">
                        <select name="entitybrand[endproducttypes][]">{$endproducttypes_list}</select>
                    </div>
                </div>

                    <div style="display:table-row;">
                        <div style="display:table-cell;"> <input type='button' id='perform_entities/managebrands_Button' value='{$lang->savecaps}' class='button'/></div>
                    </div>
                    <div style="display:table-row;"> <div style="display:table-cell;"> <div id="perform_entities/managebrands_Results"></div></div></div>

                </div>

        </form>
    </div>
</body>
</html>