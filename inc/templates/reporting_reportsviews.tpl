<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->reportsviews}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>Q{$quarter[quarter]} {$quarter[year]} Views</h1>
            {$views_outputs}
        </td>
    </tr>
    {$footer}
</body>
</html>