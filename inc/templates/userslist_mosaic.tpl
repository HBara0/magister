<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->employeeslist}</title>
        {$headerinc}
    </head>

    <body>
        {$header}
    <tr>
        <td class="menuContainer" align="left">
            <ul id="mainmenu">
                <li><span><a href="users.php?action=profile">{$lang->viewyourprofile}</a></span></li>
                <li><span><a href="users.php?action=profile&amp;do=edit">{$lang->manageyouraccount}</a></span></li>
            </ul>
        </td>
        <td class="contentContainer">
            <div style="float:right;text-align:center; width: 100px; display:inline-block; margin:10px; vertical-align:top;"><a href="{$change_view_url}"><img src="./images/icons/{$change_view_icon}" alt="{$lang->changeview}" border="0"/></a></div>
            <h3>{$lang->employeeslistmosaiicview}</h3>
            <div id="mosaicgrid">
                {$userslistmosaic_row}
            </div>
        </td>
    </tr>
    {$footer}
</body>
</html>