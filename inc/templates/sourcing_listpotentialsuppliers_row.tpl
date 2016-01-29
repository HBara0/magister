<tr class='{$rowclass}' style="vertical-align:top;">
    <td><a href="index.php?module=sourcing/supplierprofile&amp;id={$potential_supplier[supplier][ssid]}" target="_blank">{$potential_supplier[supplier][companyName]}</a></td>
<input id="ssid_{$potential_supplier[ssid]}" type="hidden" value="{$potential_supplier[ssid]}" />
<td>{$potential_supplier[supplier][type]}</td>
<td>{$potential_supplier[segments_output]}</td>
<td>{$potential_supplier[activityarea_output]}</td>
<td id="{$potential_supplier[ssid]}">{$rating_section}</td>
<td>{$potential_supplier[isactive_output]}</td>
<td>{$edit_link}{$checkbox}</td>
</tr>
