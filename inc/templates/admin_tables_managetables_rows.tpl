<tr>
    <td><input type="text" name="column_data[{$column_data['columnDbName']}][columnDbName]" value="{$column_data['columnDbName']}">
        <input type='hidden' name='column_data[{$column_data['columnDbName']}][stid]' value='{$column_data['stid']}'>
        <input type='hidden' name='column_data[{$column_data['columnDbName']}][stcid]' value='{$column_data['stcid']}'>
        <input type='hidden' name='column_data[{$column_data['columnDbName']}][extra]' value='{$column_data['extra']}'>

    </td>
    <td><input required="required" type="text" id="restricted" name="column_data[{$column_data['columnDbName']}][columnSystemName]" value="{$column_data['columnSystemName']}">
    </td>
    <td><input type="text" name="column_data[{$column_data['columnDbName']}][columnTitle]" value="{$column_data['columnTitle']}">
    </td>
    <td>{$type_selectlist}
    </td>
    <td><input type="text" accept="numeric" name="column_data[{$column_data['columnDbName']}][length]" value="{$column_data['length']}">
    </td>
    <td><input type="checkbox" name="column_data[{$column_data['columnDbName']}][isPrimaryKey]" {$primary_check} value="1">
    </td>
    <td><input type="checkbox" name="column_data[{$column_data['columnDbName']}][isUnique]" {$unique_check} value="1">
    </td>
    <td><input type="checkbox" name="column_data[{$column_data['columnDbName']}][isRequired]" {$required_check} value="1">
    </td>
    <td><input type="checkbox" name="column_data[{$column_data['columnDbName']}][isSimple]" {$simple_check} value="1">
    </td>
    <td><input type="radio" name="displayName" {$required_check} value="{$column_data['columnDbName']}">
    </td>
    <td>{$reference_selectlist}
    </td>
</td>
</tr>
