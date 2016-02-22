<div class="container">
    <h1>{$lang->cmssettings}</h1>
    <form id="change_cms/cmssettings_Form" name="change_cms/cmssettings_Form" action="#" method="post">
        <table class="datatable">
            {$settingslist}
            <tr>
                <td colspan="2">
                    <input type="button" id="change_cms/cmssettings_Button" value="{$lang->savecaps}" /> <input type="reset" value="{$lang->reset}" />
                    <div id="change_cms/cmssettings_Results"></div>
                </td>
            </tr>
        </table>
    </form>
</div>