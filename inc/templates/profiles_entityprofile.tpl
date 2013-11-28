<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$profile[companyName]}</title>
        {$headerinc}
        <link href="{$core->settings[rootdir]}/css/rateit.css" rel="stylesheet" type="text/css">
        <link href="{$core->settings[rootdir]}/css/rml.css" rel="stylesheet" type="text/css">
        <style type="text/css">
            span.listitem:hover { border-bottom: #CCCCCC solid thin; }
        </style>
        <script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(function() {
                $("a[id^='loadentityusers_'],a[id^='loadallusers_']").click(function() {
                    if (sharedFunctions.checkSession() == false) {
                        return;
                    }
                    var ids = $(this).attr("id").split('_');

                    if (ids[0] == 'loadentityusers') {
                        var action = 'getentityusers';
                    }
                    else
                                {
                                                var action = 'getallusers';
                                            }

                                            sharedFunctions.requestAjax("post", "index.php?module=profiles/entityprofile&action=" + action, "affid=" + ids[1] + '&eid=' + ids[2], 'entityusers', 'entityusers', true);
                                        });
            {$header_rmljs}
            {$header_ratingjs}
                                    });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="2"><h3>{$profile[companyName]}</h3> <input type="hidden" name="eid" id="eid" value="{$profile[eid]}" /></td>
                </tr>
                <tr>
                    <td valign="top" style="width:45%; text-align:center;"><img id="logo" src="{$profile[logo]}" alt="{$profile[companyName]}" border="0" /> <hr /></td>
                    <td valign="top" class="border_left" style="width:55%;padding:10px;" rowspan="2">
                        <div class="subtitle">{$lang->contactdetails}</div>
                        {$lang->fulladdress}: {$profile[fulladdress]}<br />
                        {$lang->pobox}: {$profile[poBox]}<br />
                        {$lang->telephone}: {$phone}<br />
                        {$lang->fax}: {$fax}<br />
                        {$lang->email}: <a href="mailto:{$profile[mainEmail]}">{$profile[mainEmail]}</a><br />  
                        {$profile[website]} 
                    </td>      
                </tr>
                <tr>
                    <td>
                        {$maturity_section}
                        {$rating_section}
                    </td>
                </tr>
                <tr>
                    <td  valign="top" style="padding:10px;"><span class="subtitle">{$lang->segments}</span><br />{$segmentlist}&nbsp;</td>
                    <td  valign="top" class="border_left" style="padding:10px;">
                        <span class="subtitle">{$lang->contactperson}</span> <a href="index.php?module=profiles/representativeslist&filterby=entityid&filtervalue={$eid}"><img src="./images/icons/list_view.gif" border=0 alt="{$lang->contactperson}" /></a><br />
                            {$representativelist}
                    </td>
                </tr>
                <tr>
                    <td valign="top" style="padding:10px;"><span class="subtitle" >{$lang->affiliate}</span><span class="smalltext" > (Click on <img src='{$core->settings[rootdir]}/images/icons/people.png' alt='{$lang->show}' border='0' /> to view employees)</span><br />
                            {$affiliateslist}
                    </td>
                    <td class="border_left" valign="top" style="padding:10px;"><span class="subtitle" >{$lang->employees}</span> (<a href='#affiliate' id='loadallusers_0_{$eid}' class='smalltext'>view all</a>)
                        <div id='entityusers'>{$entityallusers}</div>
                    </td>
                </tr>
                {$products_section}
                {$meetings_section}
                {$reports_section}
                {$entityprofile_private}

            </table>
        </td></tr>
        {$footer}
</body>
</html>