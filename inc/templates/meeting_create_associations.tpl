<tr>
    <td colspan="2" id="associationssection" style="{$sectionsvisibility[associationssection]}">
        <table class="datatable" border="0" width="100%" cellspacing="1" cellpadding="1">
            <tr>
                <td>{$lang->customer}</td>
                <td> 
                    <input type='text'id='customer_1_QSearch' value="{$meeting[associations][cutomername]}" autocomplete='off' size='40px'/>
                    <input type='hidden' id='customer_1_id' name='meeting[associations][cid]' value="{$meeting[associations][cid]}" /> 
                    <div id='searchQuickResults_customer_1' class='searchQuickResults' style='display:none;'></div> </select>
                    <input type='hidden'   name='cid' value="{$meeting[associations][cid]}" />
                </td>
                <td>{$lang->supplier}</td>
                <td><input type='text'id='supplier_1_QSearch' value="{$meeting[associations][suppliername]}" autocomplete='off' size='40px'/>
                    <input type='hidden' id='supplier_1_id' name='meeting[associations][spid]' value="{$meeting[associations][spid]}" /> 
                    <div id='searchQuickResults_supplier_1' class='searchQuickResults' style='display:none;'></div>
                </td>
            </tr>
            <tr>
                <td>{$lang->affiliate}</td> 
                <td>{$affiliates_list}</td>
                <td >{$lang->event}</td>
                <td>
                    <!--<select name="meeting[associations][ceid]">{$events_list}-->{$lang->na}</select> 
                </td>
            </tr>
            <tr>
                <td>{$lang->businessleave}</td>
                <td><!--<select name="meeting[associations][lid]">{$business_leaves_list}</select>-->{$lang->na}</td>
            </tr>
            <tr>
                <td>{$lang->country}</td>
                <td>{$countries_list}</td>
                <td>&nbsp;</td>
            </tr>
        </table>          
    </td>
</tr>