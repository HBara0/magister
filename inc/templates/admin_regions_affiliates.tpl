<h1>{$lang->listavailableaffiliates}</h1>
         <!-- <div style="float: right"><a target='_blank' href="{$core->settings['rootdir']}/manage/index.php?module=regions/affiliates&action=update_charspec"><img alt="update" src="{$core->settings['rootdir']}/images/icons/update.png"></a></div> -->
<table class="datatable">
    <thead>
        <tr>
            <th>{$lang->id}</th><th>{$lang->name}</th><th>{$lang->countries}</th>
        </tr>
    </thead>
    <tbody>
        {$affiliates_list}
    </tbody>
</table>
<hr />
<h1>{$lang->addaffiliate}</h1>
<form id="add_regions/affiliates_Form" name="add_regions/affiliates_Form" action="#" method="post">
    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td width="20%">{$lang->name}</td>
            <td width="80%"><input type="text" id="name" name="name" /></td>
        </tr>
        <tr>
            <td width="20%">{$lang->countries}</td>
            <td width="80%">{$countries_list}</td>
        </tr>
        <tr>
            <td>{$lang->description}</td><td><textarea cols="30" rows="5" id="description" name="description"></textarea></td>
        </tr>
        <tr>
            <td colspan="2"><hr /><span class="subtitle">{$lang->mainofficeinfo}</span></td>
        </tr>
        <tr>
            <td width="20%">{$lang->city}</td>
            <td width="80%"><input type="text" id="city" name="city" /></td>
        </tr>
        <tr>
            <td width="20%">{$lang->address}</td>
            <td width="80%"><input type="text" id="addressLine1" name="addressLine1" /><br /><input type="text" id="addressLine2" name="addressLine2" /></td>
        </tr>
        <tr>
            <td colspan="2"><hr /><span class="subtitle">{$lang->affiliatemanagement}</span></td>
        </tr>
        <tr>
            <td>{$lang->mailinglistaddress}</td>
            <td>
                <input type="text" id="email" name="mailingList"/> <span id="email_Validation"></span>
            </td>
        </tr>
        <tr>
            <td>{$lang->affiliatesupervisor}</td>
            <td>
                <input type='text' id='user_2_autocomplete' autocomplete='off' size='40px'/><input type='hidden' id='user_2_id' name='supervisor'/><div id='searchQuickResults_2' class='searchQuickResults' style='display:none;'></div>
            </td>
        </tr>
        <tr>
            <td>{$lang->affiliategm}</td>
            <td>
                <input type='text' id='user_1_autocomplete' autocomplete='off' size='40px'/><input type='hidden' id='user_1_id' name='generalManager'/><div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div>
            </td>
        </tr>
        <tr>
            <td>{$lang->affiliatehr}</td>
            <td>
                <input type='text' id='user_3_autocomplete' autocomplete='off' size='40px'/><input type='hidden' id='user_3_id' name='hrManager'/><div id='searchQuickResults_3' class='searchQuickResults' style='display:none;'></div>
            </td>
        </tr>
        <tr>
            <td colspan="2"><input type="button" id="add_regions/affiliates_Button" value="{$lang->add}" /><input type="reset" value="{$lang->reset}" />
                <div id="add_regions/affiliates_Results"></div>
            </td>
        </tr>
    </table>
</form>

