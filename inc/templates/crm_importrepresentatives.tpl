<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->importrepresentatives}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->importrepresentatives}</h3>
            <table width="100%">
                <tr>
                    <td>
                        <iframe id='uploadFrame' name='uploadFrame' src='#' style="display:none;"></iframe>
                        <p>
                        <form id='perform_crm/importrepresentatives_Form' name='perform_crm/importrepresentatives_Form' action='index.php?module=crm/importrepresentatives&amp;action=preview' method='post' enctype='multipart/form-data' target='uploadFrame'>
                            <strong>{$lang->selectcustomersfile}:</strong> <input type='file' id='uploadfile' name='uploadfile'> <span style='font-style:italic; margin: 5px;'>({$lang->importcustomersallowedtypes})</span>
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
        </td>
    </tr>
    {$footer}
</body>
</html>