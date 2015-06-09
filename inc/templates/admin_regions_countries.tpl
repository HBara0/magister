<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listavailablecountries}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->listavailablecountries}</h1>
            <table class="datatable">
                <div style="float: right;" title="{$lang->addcountry}">
                    <a href="#popup_addcountry" id="showpopup_addcountry" class="showpopup"><img alt="Add Country" src="{$core->settings['rootdir']}/images/icons/edit.gif" /></a>
                    <a href="http://127.0.0.1/ocos/manage/index.php?module=regions/countries&action=update_countrydetails" id=""><img alt="Add Country" src="{$core->settings['rootdir']}/images/icons/invalid.gif" /></a>
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
        </td>
    </tr>
    {$footer}
    {$addcountry}
</body>
</html>