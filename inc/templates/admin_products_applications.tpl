<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listavailableapplication}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->listavailableapplication}</h3> 
            <table class="datatable">
                <div style="float:right;" class="subtitle"> <a href="#" id="showpopup_creteapplication" class="showpopup"><img  src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->create}</a></div>
                <thead>
                    <tr>   
                        <th>{$lang->name}<a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                        <th>{$lang->title}<a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="../images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                        <th>{$lang->segment} </th>
                        <th>{$lang->desc} </th>

                    </tr>
                </thead>
                <tbody>
                    {$productsapplications_list}
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
        </form>
    </td>
</tr>
{$footer}
</body>
</html>

<div id="popup_creteapplication"  title="{$lang->create}">

    <form action="#" method="post" id="perform_products/applications_Form" name="perform_products/applications_Form">
        <input type="hidden" name="action" value="do_create" />
        <table cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td width="40%"><strong>{$lang->name}</strong></td><td> <input name="segmentapplications[title]"  type="text"/></td>
            </tr>
            <tr>
                <td width="40%"><strong>{$lang->segment}</strong></td><td><select name="segmentapplications[psid]">{$segments_list}</select></td>
            </tr>
            <tr>
                <td><strong>{$lang->functions}</strong></td><td><select name="segmentapplications[segappfunctions][]" multiple="true">{$checmicalfunctions_list}</select></td>
            </tr>
            <tr>
                <td colspan="2" align="left">
                    <hr />
                    <input type='button' id='perform_products/applications_Button' value='{$lang->savecaps}' class='button'/>
                    <div id="perform_products/applications_Results"></div>
                </td>
            </tr>

            </div>