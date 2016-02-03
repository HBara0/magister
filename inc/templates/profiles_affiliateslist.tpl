<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->affiliateslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <div style="width:40%; display:inline-block;"><h1>{$lang->affiliateslist}</h1></div><div style="float:right; display:inline-block; width:40%; text-align:right;"><br /><a href="index.php?module=profiles/affiliateslist&view={$switchview[link]}"><img src="./images/icons/{$switchview[icon]}" alt="{$lang->changeview}" border="0"></a></div>
            <table class="datatable_basic table table-bordered row-border hover order-column" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th >{$lang->name}</th>
                        <th >{$lang->territory}</th>
                        <th >{$lang->gm}</th>
                        <th >{$lang->supervisor}</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th >{$lang->name}</th>
                        <th >{$lang->territory}</th>
                        <th >{$lang->gm}</th>
                        <th >{$lang->supervisor}</th>
                    </tr>
                </tfoot>
                <tbody>
                    {$affiliates_list}
                </tbody>
            </table>
            <div align="center">{$map_view}</div>
        </td>
    </tr>
    {$footer}
</body>
</html>