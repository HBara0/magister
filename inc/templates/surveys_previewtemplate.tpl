<head>
    <title>{$core->settings[systemtitle]} | {$lang->previewtemplate}</title>
    {$headerinc}
</head>
<body>
    {$header}
<tr>
    {$menu}
    <td class="contentContainer">
        <h1>{$lang->previewtemplate} - {$templates['title']}</h1>
        {$questions_list}
        <hr />
        <button onclick="window.close()" class="button">{$lang->close}</button>
    </td>
</tr>
{$footer}
</body>
</html>