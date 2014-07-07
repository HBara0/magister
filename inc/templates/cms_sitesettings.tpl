<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->modifysitesettings}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->modifysitesettings}</h1>
            <form id="change_cms/settings_Form" name="change_cms/settings_Form" action="#" method="post">
                <table class="datatable">
                    {$settingslist}
                    <tr>
                        <td colspan="2">
                            <input type="button" id="change_cms/settings_Button" value="{$lang->savecaps}" /> <input type="reset" value="{$lang->reset}" />
                            <div id="change_cms/settings_Results"></div>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>