<span style="margin-right:10px;"><a href="{$_SERVER[HTTP_REFERER]}">&laquo; {$lang->goback}</a></span></td>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td colspan="2"><h1>{$profile[firstName]}, {$profile[middleName]} {$profile[lastName]}  ({$profile[displayName]})</h1></td>
    </tr>
    <tr>
        <td valign="top" style="width:35%; text-align:center;">
            {$profile[picture]}
            <hr />{$profile_profilepicform}</td>
        <td valign="top" class="border_left" style="padding: 10px;">
            <div class="subtitle">{$lang->contactinformation} <a style="cursor:pointer;" href="{$core->settings['rootdir']}/users.php?action=downloadvcard&uid={$profile[uid]}"><img src="./images/editprofile.gif" title="{$lang->downloadcontact}"/></a></div>
            {$lang->fulladdress}: {$profile[fulladdress]}<br />
            {$lang->pobox}: {$profile[poBox]}<br />
            {$lang->internalextension}: {$profile[internalExtension]}<br />
            {$lang->telephone}: {$profile[telephone]}<br />
            {$lang->telephone} 2: {$profile[telephone2]}<br />
            {$lang->mobile}: {$profile[mobile_output]}{$profile[mobile2_output]}<br />
            {$lang->email}: <a href="mailto:{$profile[email]}">{$profile[email]}</a><br />
            {$lang->bbpin}: {$profile[bbPin]}<br />
            {$profile[skype_output]}
        </td>
    </tr>
    <tr>
        <td class="border_left" style="padding: 5px;"><span class="subtitle">{$lang->employeeinformation}</span></td>
        <td class="border_left" style="padding: 5px;"><span class="subtitle">{$lang->workswith}</span></td>
    </tr>
    <tr>
        <td valign="top">
            {$lang->position}: {$profile[position]}<br />
            {$lang->reportsto}: <a href='users.php?action=profile&amp;uid={$profile[reportsTo]}'>{$profile[reportsToName]}</a><br />
            {$assistant_details}
            {$lang->mainaffiliate}: {$profile[mainaffiliate][name]}<br />
            <p><span style="font-weight:bold;">{$lang->affiliate}</span><a name="affiliates"></a><br />
                {$profile[affiliatesList]}</p>
            <p><span style="font-weight:bold;">{$lang->segments}</span><a name="segments"></a><br />
                    {$profile[segmentsList]}
            </p>
        </td>
        <td rowspan="3" valign="top" class="border_left" >
            <p><span style="font-weight:bold;">{$lang->supplier}</span><a name="suppliers"></a><br />
                {$profile[suppliersList]}</p>
            <p><span style="font-weight:bold;">{$lang->customer}</span><a name="customers"></a><br />
                {$profile[customersList]}</p>
        </td>
    </tr>
    <tr><td colspan="2">{$jobdescription_section}</td></tr>
        {$userprofile_private}
</table>