<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$segment[title]}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <div><h3>{$segment[title]}</h3></div>
            <div style="display:inline-block; width: 45%; vertical-align: top;"><div class="subtitle">{$lang->applications}</div>{$segment_applications}</div>
            <div style="display:inline-block; width: 45%; vertical-align: top;">
                <span class="subtitle">{$lang->coordinators}</span>
                {$segment_coordinators}
                <br />
                <span class="subtitle">{$lang->endproduct}</span>
                {$endproduct_types}
            </div>
            <hr />
            <div style="display:inline-block; width: 45%; vertical-align: top;"><span class="subtitle">{$lang->employees}</span>
                {$segment_employees}</div>
            <div style="display:inline-block; width: 45%; vertical-align: top;"><span class="subtitle">{$lang->suppliers}</span>
                {$segment_suppliers}</div>
            <hr />
        </td>
    </tr>
    {$footer}
</body>
</html>