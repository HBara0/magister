<style type="text/css">
    span.listitem:hover { border-bottom: #CCCCCC solid thin; }
</style>
<script type="text/javascript">
    $(function() {
        $("a[id^='loadentityusers_'],a[id^='loadallusers_']").click(function() {
            if(sharedFunctions.checkSession() == false) {
                return;
            }

            var ids = $(this).attr("id").split('_');

            if(ids[0] == 'loadentityusers') {
                var action = 'getentityusers';
            }
            else
            {
                var action = 'getallusers';
            }
            sharedFunctions.requestAjax("post", "index.php?module=profiles/affiliateprofile&action=" + action, "eid=" + ids[1] + '&affid=' + ids[2], 'entityusers', 'entityusers', true);
        });
    });
</script>
<script src="{$core->settings[rootdir]}/js/profiles_marketintelligence.min.js" type="text/javascript"></script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td colspan="2"><h1>{$profile[name]}</h1></td>
    </tr>
    <tr>
        <td valign="top" style="width:50%; padding:10px;">
            <div class="subtitle" style="margin-bottom:5px;">{$lang->contactdetails}</div>
            {$lang->fulladdress}: {$profile[fulladdress]}<br />
            {$lang->pobox}: {$profile[poBox]}<br />
            {$lang->telephone}: {$profile[phone1]}{$profile[phone2]} (<a href='#' id='showpopup_internalextensionslist' class="showpopup">{$lang->internalextensions}</a>)<br />
            {$lang->fax}: {$profile['fax']}<br /><br />
            {$lang->infomail}: {$profile[mainEmail]}<br />
        </td>
        <td valign="top" style="width:50%; padding:10px;">
            <div class="subtitle" style="margin-bottom:5px;">{$lang->management}</div>
            <span style="font-weight:bold;">{$lang->gm}</span>: {$profile[generalManager_output]}<br />
            <span style="font-weight:bold;">{$lang->supervisor}</span>: {$profile[supervisor_output]}<br />
            <span style="font-weight:bold;">{$lang->finmanager}</span>: {$profile[finManager_output]}<br />
            <span style="font-weight:bold;">{$lang->hrmanager}</span>: {$profile[hrManager_output]}<br /><br />
            {$lang->workshift}: {$profile[workshift]}
        </td>
    </tr>
    <tr>
        <th colspan="2" style="text-align:center;" class="thead">{$lang->businessinfo}</th>
    </tr>
    <tr>
        <td colspan="2" style="padding:10px;"><span style="font-weight:bold;">{$lang->businessregions}</span>: {$countries_list}
        </td>
    </tr>
    <tr>
        <td><span class="subtitle" >{$lang->suppliers}</span></td>
        <td><span class="subtitle">{$lang->employees}</span> (<a href='#supplier' id='loadallusers_0_{$affid}' class='smalltext'>view all</a>)</td>
    </tr>
    <tr>
        <td valign="top">{$supplierslist}</td><td valign="top"><div id='entityusers'>{$supplierallusers}</div></td>
    </tr>
    {$private_section}
    {$mkintl_section}
</table>
<div id='popup_internalextensionslist' title="{$lang->internalextensions}">
    <table width="100%" class="datatable">
        {$extensions}
    </table>
</div>

{$popup_createbrand}
{$popup_marketdata}
