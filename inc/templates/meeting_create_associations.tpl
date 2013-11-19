<tr>
    <td colspan="2" id="associationssection" style="display:none;">
        <table class="datatable" border="0"  width="100%" cellspacing="1" cellpadding="1">
            <tr >
                <td>{$lang->customer}</td>
                <td> <input type='text'id='customer_1_QSearch'  value="{$meeting[attendees][cutomername]}" autocomplete='off' size='40px'/>
                    <input type='hidden' id='customer_1_id' name='meeting[attendees][cid]' value="{$meeting[attendees][cid]}" /> 
                    <div id='searchQuickResults_customer_1' class='searchQuickResults' style='display:none;'></div> </select></td>
                <td>{$lang->supplier}</td>
                <td><input type='text'id='supplier_1_QSearch'  value="{$meeting[attendees][suppliername]}" autocomplete='off' size='40px'/>
                    <input type='hidden' id='supplier_1_id' name='meeting[attendees][spid]' value="{$meeting[attendees][spid]}" /> 
                    <div id='searchQuickResults_supplier_1' class='searchQuickResults' style='display:none;'></div>  </td>
            </tr>
            <tr>
                <td>{$lang->affiliate}</td> 
                <td>{$affiliates_list}</td>
                <td >{$lang->event}</td>
                <td>
                    <select name="meeting[attendees][eventid]">{$event_list}</select> 
                </td>
            </tr>
            <tr>
                <td>{$lang->businessleave}</td>
                <td>{$business_leaves_list}</td>
              
            </tr>
            <tr>
                <td>{$lang->country}</td>
                <td>{$countries_list}</td>
                <td>&nbsp;</td>
            </tr>
        </table>          
    </td>
</tr>