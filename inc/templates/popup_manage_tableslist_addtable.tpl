<div id="popup_addsystable" title="{$lang->addtable}">
    <form name='add_tablesdefinition/tableslist_Form' id="add_tablesdefinition/tableslist_Form" method="post" >
        <input type="hidden" id="action" name="action" value="do_addtable" />
        <table width="100%">
            <tr>
            <input type="hidden" name="table_data[stid]" value="{$table_data['stid']}">
            <td>{$lang->tablename}</td><td><input type="text" name="table_data[tableName]" value="{$table_data['tableName']}"></td>
            </tr>
            <tr>
                <td>{$lang->classname}</td><td><input type="text" name="table_data[className]" value="{$table_data['className']}"></td>
            </tr>
            <tr>
                <td>{$lang->numberofcolumns}</td><td><input type="text" name="table_data[nbOfColumns]" value="{$table_data['nbOfColumns']}"></td>
            </tr>
        </table>
        <input type='button' class='button' value='{$lang->save}' id='add_tablesdefinition/tableslist_Button' />
    </form>
    <div id="add_tablesdefinition/tableslist_Results" ></div>
</div>