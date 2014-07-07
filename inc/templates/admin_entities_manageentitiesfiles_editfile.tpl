<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->modifyfileinformation}</title>
        {$headerinc}
    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->modifyfileinformation}</h1>
            <form action="#" method="post"  id="perform_entities/manageentitiesfiles_Form" name="edit_entities/manageentitiesfiles_Form" >
                <input type="hidden" id="action" name="action" value="do_modifyfileinfo">
                <input type="hidden" id="fid" name="fid" value="{$core->input['fid']}" />
                {$addedit_form}
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3"><input type="button" id="perform_entities/manageentitiesfiles_Button" value="{$lang->savecaps}" class="button"><br />
                        <div id="perform_entities/manageentitiesfiles_Results" ></div>
                    </td>
                </tr>
            </table>
        </form>
    </td>
</tr>
{$footer}
</body>
</html>