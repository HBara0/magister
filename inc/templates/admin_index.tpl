<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->welcomeap}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->welcomeap}</h3> 
            {$newsuggestions}
            <strong>{$lang->systemoverview}</strong>
            <ul>
                <li>{$lang->usersstats}</li>
                <li>{$lang->entitiesstats}
                    <ul>
                        <li>{$lang->suppliersstats}</li>
                        <li>{$lang->customersstats}</li>
                    </ul>
                </li>
                <li>{$lang->productsstats}</li>
            </ul>
            <br />
            <hr />
            <p class="subtitle">{$lang->numusersonline}</p>
            {$onlineusers}
        </td>
        <td>&nbsp;</td>
    </tr>
    {$footer}
</body>
</html>