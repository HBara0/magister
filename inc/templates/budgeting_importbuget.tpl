<div class="container">
    <h1>{$lang->importbudget}</h1>
    <table width="100%">
        <tr>
            <td><iframe id='uploadFrame' name='uploadFrame' src='#' style="display:block;"></iframe>
                <p>
                <form id='perform_budgeting/previewimport_Form' name='perform_budgeting/previewimport_Form' action='index.php?module=budgeting/importbudget&amp;action=preview' method='post' enctype='multipart/form-data' target='uploadFrame'>
                    <strong>{$lang->selectbudgetfile}:</strong>
                    <input type='file' id='uploadfile' name='uploadbudget'> <span style='font-style:italic; margin: 5px;'>({$lang->importbudgetallowedtypes})</span><br />

                    <br />{$lang->delimiter}: <input type="text" size="2" value="," name="delimiter" id="delimiter"> | {$lang->multivalueseperator}: <input type="text" size="2" value="|" name="multivalueseperator" id="multivalueseperator" />
                    <br />
                    <br /><input type='submit' class='button' value="{$lang->next}" onClick="$('#upload_Result').show();">
                </form>
                <hr />
                </p>
                <div id='upload_Result' style='display:none;'><img src="{$core->settings[rootdir]}/images/loading-bar.gif" /> {$lang->uploadinprogress}</div>
            </td>
        </tr>
    </table>

</div>