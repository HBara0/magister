<h1>{$lang->listavailablecountries}</h1>
<table class="datatable">
    <div style="float: right;" title="{$lang->addcountry}">
        <a href="#popup_addcountry" id="showpopup_addcountry" class="showpopup"><img alt="Add Country" src="{$core->settings['rootdir']}/images/icons/edit.gif" /></a>
    </div>
    <div style="float: right" title="{$lang->updatecountrydata}">
        <a href="{$core->settings[rootdir]}/manage/index.php?module=regions/countries&action=update_countrydetails" target="_blank" id=""><img alt="{$lang->updatecountrydata}" src="{$core->settings['rootdir']}/images/download.png" /></a>
    </div>
    <thead>
        <tr>
            <th>{$lang->id}</th><th>{$lang->name}</th><th>{$lang->affiliate}</th><th></th>
        </tr>
    </thead>
    <tbody>
        {$countries_list}
    </tbody>
</table>
<hr />
{$addcountry}
