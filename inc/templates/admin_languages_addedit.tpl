<h1>$lang->managelanguagefile</h1>
<form action="#" method="post" id="perform_languages/manage_Form" name="perform_languages/manage_Form">
    <input type="hidden" value="do_{$actiontype}languages" name="action" id="action" />
    <table width="100%" class="datatable">
        <tr>
            <td class="thead">{$lang->filename}: {$core->input[filename]}<input type="{$filename_inputtype}" name='filename' value="{$core->input[filename]}"></td>
        </tr>
        {$edit_langvariables}
    </table>
    <br />
    <table width="100%" class="datatable">
        <tr>
            <td class="thead">{$lang->addlangvar}</td>

        </tr>
        <tbody id="variables_1_tbody">
            {$add_langvariables}
        </tbody>
        <tfoot>
        <td><img src="../images/add.gif" id="addmore_variables_1" alt="{$lang->add}"></td>
        </tfoot>
    </table>
    <input type="button" id="perform_languages/manage_Button" value="{$lang->savecaps}"/>
</form>
<div id="perform_languages/manage_Results"></div>