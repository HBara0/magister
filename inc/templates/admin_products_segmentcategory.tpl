<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listsegmentcategoriess}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->listsegmentcategoriess}</h1>
            <div style="float:right;" class="subtitle"><a href="#" id="showpopup_addsegmentcat" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->addasegmentcat}</a></div>
            <table class="datatable">
                <thead>
                    <tr>
                        <th>{$lang->id}</th><th>{$lang->title}</th><th>{$lang->description} </th>
                    </tr>
                </thead>
                <tbody>
                    {$segments_list}
                </tbody>
            </table>
            <hr />
        </td>
    </tr>
    {$footer}
    {$addsegmentcat}
</body>
</html>