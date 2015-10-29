<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->facilitytree}</title>
        {$headerinc}

    </head>
    <body>
        {$header}

    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->list}</h3>
            <form action='index.php?module=facilityngnt/list' method="post">
                <table class="datatable">
                    <div style="float:right;" class="subtitle"> <a target="_blank" href="{$core->settings[rootdir]}/index.php?module=facilitymgmt/managefacility" ><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->create}</a></div>
                    <thead>
                        <tr>
                            <th><span  style="margin-left:30px;">{$lang->name} - {$lang->affiliate}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>{$facilities_list}</td></tr>
                    </tbody>
                </table>
            </form>

        </td>
    </tr>
    {$footer}
</body>

</html>