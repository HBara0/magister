<h1>{$lang->importstockspagetitle}</h1>
<table width="100%">
    <tr>
        <td><iframe id='uploadFrame' name='uploadFrame' src='#' style='display:none'></iframe>
            <p>
            <form id='perform_stock/import_Form' name='perform_stock/import_Form' action='index.php?module=stock/import&amp;action=import' method='post' enctype='multipart/form-data' target='uploadFrame'>
                <div style="display:inline-block; width: 20%;"><strong>{$lang->selectattfile}:</strong></div><div style="display:inline-block;; width: 75%;"> <input type='file' id='uploadfile' name='uploadfile'> <span style='font-style:italic; margin: 5px;'>({$lang->importstockallowedtypes})</span></div>
                <div style="display:inline-block; width: 20%;">{$lang->dateformat}:</div><div style="display:inline-block;; width: 75%;">{$dateformats_selectlist} | {$lang->delimiter}: <input type="text" size="2" value="," name="delimiter" id="delimiter"></div>
                <div style="display:inline-block; width: 20%;">{$lang->affiliate}:</div><div style="display:inline-block;; width: 75%;">{$affiliates_list}</div>
                <div style="display:inline-block; width: 20%;">{$lang->system}:</div><div style="display:inline-block;; width: 75%;"><select name="foreignSystem" id="foreignSystem"><option value="1">Outsys</option><option value="2">Sage Accpac</option></select></div>
                <fieldset style="display:block; clear:both; margin-top: 10px; margin-bottom: 10px;" class="altrow2">
                    <legend>{$lang->importoptions}:</legend>

                    <input type="checkbox" name="resolveproductname" /> {$lang->resolveproductname}
                    <br /><input type="checkbox" name="resolvesuppliername" /> {$lang->resolvesuppliername}
                </fieldset>
                <br /><input type='submit' class='button' value="{$lang->importstockbutton}" onClick="$('#upload_Result').show();">
            </form>
            <hr />
            </p>
            <div id='upload_Result' style='display:none;'><img src="{$core->settings[rootdir]}/images/loading.gif" /> {$lang->uploadinprogress}</div>
        </td>
    </tr>
</table>