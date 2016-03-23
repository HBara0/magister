<h1>{$lang->importcustomers}</h1>
<table width="100%">
    <tr>
        <td><iframe id='uploadFrame' name='uploadFrame' src='#'></iframe>
            <p>
            <form id='perform_crm/previewimport_Form' name='perform_crm/previewimport_Form' action='index.php?module=crm/importcustomers&amp;action=preview' method='post' enctype='multipart/form-data' target='uploadFrame'>
                <strong>{$lang->selectcustomersfile}:</strong> <input type='file' id='uploadfile' name='uploadcustomers'> <span style='font-style:italic; margin: 5px;'>({$lang->importcustomersallowedtypes})</span><br />
                <strong>{$lang->selectrepresentativesfile}:</strong> <input type='file' id='uploadfile' name='uploadrepresentatives'> <span style='font-style:italic; margin: 5px;'>({$lang->importrepresentativesallowedtypes})</span>
                <br />{$lang->delimiter}: <input type="text" size="2" value="," name="delimiter" id="delimiter"> | {$lang->multivalueseperator}: <input type="text" size="2" value="|" name="multivalueseperator" id="multivalueseperator" />
                <br />
                <br /><input type='submit' class='button' value="{$lang->next}" onClick="$('#upload_Result').show();">
            </form>
            <hr />
            </p>
            <div id='upload_Result' style='display:none;'><img src="{$core->settings[rootdir]}/images/loading.gif" /> {$lang->uploadinprogress}</div>
        </td>
    </tr>
</table>