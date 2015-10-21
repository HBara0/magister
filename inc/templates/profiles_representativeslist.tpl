<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->representativeslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->representativeslist}</h1>
            <form name="perform_profiles/representativeslist_Form" id="perform_profiles/representativeslist_Form" action="#" method="post">
                <div style="float: right">
                    <div style="float: right;"><a href="#popup_editrepresentative" id="showpopup_editrepresentative" class="showpopup"><button type="button">{$lang->addrepresentative}</button></a></div>
                </div>

                <table class="datatable">
                    <thead>
                        <tr>
                            <th style="width:20%">{$lang->name}<a href="{$sort_url}&amp;sortby=name&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=name&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                            <th style="width:20%">{$lang->email}<a href="{$sort_url}&amp;sortby=email&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=email&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                            <th style="width:15%">{$lang->telephone}<a href="{$sort_url}&amp;sortby=phone&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=phone&amp;order=DESC"><img src="./images/sort_desc.gif" border="0"  alt="{$lang->sortdesc}"/></a></th>
                            <th style="width:20%">{$lang->companyname}</th>
                            <th style="width:25%">{$lang->position}</th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr>{$filters_row}</tr>
                    </thead>
                    <tbody>
                        {$representatives_list}
                    </tbody>
                </table>
            </form>
        </td>
    </tr>
    {$footer}
    {$create}
</body>
</html>