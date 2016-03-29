<tr class='{$rowclass}'>
    <td>{$type_icon}</td>
    <td><a href='index.php?module=filesharing/fileslist&amp;action=download&amp;alias={$file[filealias]}{$url_fileversion}' target="_blank">{$file[filetitle]}</a></td>
    <td>{$description}</td>
    <td>{$file[date_output]}</td>
    <td>{$file[category]}</td>
    <td>{$file[size]}</td>
    <td style="text-align:right;">
        {$tools}
    </td>
</tr>