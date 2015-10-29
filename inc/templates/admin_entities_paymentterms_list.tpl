<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->managepaymentterms}</title>
        {$headerinc}
    </head>
    <body>
        {$header}

    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->managepaymentterms}</h3>
            <div style="float:right;" class="subtitle">
                <a href="#" id="showpopup_addpaymentterms" class="showpopup">
                    <img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->createpaymentterms}</a></div>
            <table class="datatable" width="100%">
                <thead>

                    <tr>
                        <th width="40%">{$lang->title} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="../images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="../images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>
                        <th width="40%">{$lang->overduepaymentdays}</th>
                        <th width="15%">{$lang->nextbusinessday}</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                    {$paymentterms_rows}
                </tbody>
            </table>
            <div style="width:40%; float:left; margin-top:20px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
                </form>
            </div>
        </td>
    </tr>
    {$popup_addpaymentterms}
</body>
</html>