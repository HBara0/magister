<div class="container">
    <h1>{$lang->importattendance}</h1>
    <table width="100%">
        <tr>
            <td><iframe id='uploadFrame' name='uploadFrame' src='#' style='display:none' ></iframe>
                <p>
                <form id='perform_attendance/import_Form' name='perform_attendance/import_Form' action='index.php?module=attendance/importattendance&amp;action=import' method='post' enctype='multipart/form-data' target='uploadFrame'>
                    <strong>{$lang->selectattfile}:</strong> <input type='file' id='uploadfile' name='uploadfile'> <span style='font-style:italic; margin: 5px;'>({$lang->importattendanceallowedtypes})</span>
                    <br />{$lang->dateformat}: {$dateformats_selectlist} | {$lang->delimiter}: <input type="text" size="2" value="," name="delimiter" id="delimiter">
                    <br />
                    <br /><input type='submit' class='button' value="{$lang->importattendance}" onClick="$('#upload_Result').show();">
                </form>
                <hr />
                </p>
                <div id='upload_Result' style='display:none;'><img src="{$core->settings[rootdir]}/images/loading.gif" /> {$lang->uploadinprogress}</div>
            </td>
        </tr>
    </table>
</div>