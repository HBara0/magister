<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listavailablesegments}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->listavailablesegments}</h1>
            <div style="float:right;" class="subtitle"><a href="#" id="showpopup_addsegment" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->addasegment}</a></div>
            <form method='post' action='$_SERVER[REQUEST_URI]'>
                <table class="datatable">
                    <thead>
                        <tr>
                            <th>{$lang->id}</th><th>{$lang->title}</th><th>{$lang->description} </th>
                        </tr>
                        <tr>
                            {$filters_row}
                        </tr>
                    </thead>
                    <tbody>
                        {$segments_list}
                    </tbody>
                </table>
            </form>
            <hr />
        </td>
    </tr>
    {$footer}
    {$addsegment}
</body>
</html>