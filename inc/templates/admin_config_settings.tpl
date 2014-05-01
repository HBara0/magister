<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->modifysettings}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->modifysettings}</h3>
            <form id="change_config/settings_Form" name="change_config/settings_Form" action="#" method="post">
                <table class="datatable">
                    {$settingslist}
                    <tr><td colspan="2"><input type="button" id="change_config/settings_Button" value="{$lang->modify}" /> <input type="reset" value="{$lang->reset}" />
                            <div id="change_config/settings_Results"></div>
                        </td></tr>
                </table>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>