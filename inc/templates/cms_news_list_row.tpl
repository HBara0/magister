<tr class='{$rowclass}'>
    <td><a href='index.php?module=cms/managenews&amp;type=edit&amp;newsid={$news[cmsnid]}' target="_blank" title="{$news[title]}">{$news[title]}</a></td>
    <td width="6%">{$news[version]}</td>
    <td width="6%">{$ispublished_icon}</td>
    <td width="6%">{$isfeatured_icon}</td>
    <td width="12%">{$news[creator]}</td>
    <td width="12%">{$news[dateCreated_output]}</td>
    <td width="6%">{$news[lang]}</td>
    <td width="6%">{$news[hits]}</td>
</tr>