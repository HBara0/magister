<tr class="{$rowclass}">
    {$amount_output}
</tr>
<tr class="{$rowclass}">
    {$income_output}
</tr>
<tr class="{$rowclass}">
    <td style="width:25%">{$lang->accountedcommissions}/{$type->title}</td>
    <td style="width:12.5%" class="border_left">{$combudget['prevtwoyears'][$type->stid]['perc']}%</td>
    <td style="width:12.5%" class="border_left">{$combudget['prevyear'][$type->stid]['perc']}%</td>
    <td style="width:12.5%" class="border_left">{$combudget['yef'][$type->stid]['perc']}%</td>
    <td style="width:8.3%" class="border_left"><div id="placcount_{$category->name}_yefactual_{$type->stid}_perc">{$combudget[yefactual][$type->stid]['perc']}</div></td>
    <td style="width:8.3%" class="border_left"><div id="placcount_{$category->name}_yefbud_{$type->stid}_perc">{$combudget[yefbud][$type->stid]['perc']}</div></td>
    <td style="width:12.5%" class="border_left">{$combudget['current'][$type->stid]['perc']}%</td>
    <td style="width:8.3%" class="border_left"><div id="placcount_{$category->name}_budyef_{$type->stid}_perc">{$combudget[budyef][$type->stid]['perc']}</div></td>
</tr>