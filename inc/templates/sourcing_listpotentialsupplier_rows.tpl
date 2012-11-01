
<tr class='{$rowclass}'>
<td><a href="index.php?module=sourcing/supplierprofile&id={$potential_supplier[supplier][ssid]}" target="_blank">{$potential_supplier[supplier][companyName]}</a></td>
<input  id="ssid_{$potential_supplier[ssid]}" type="hidden" value="{$potential_supplier[ssid]}">
    <td>{$potential_supplier[supplier][type]}</td>
   	  <td>{$potential_supplier[segments]}</td>
    <td>{$potential_supplier[activityarea]}</td>
  <td id="{$potential_supplier[ssid]}">{$rating_section}</td>
  <td>{$edit}</td>
 <td>{$notemark}</td>
</tr>
