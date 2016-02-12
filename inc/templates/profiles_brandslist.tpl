<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->brandslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->brandslist}</h1>
            <form method='post' action='$_SERVER[REQUEST_URI]'>
                <table class="datatable_basic table table-bordered row-border hover order-column" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th >{$lang->brand}</th>
                            <th >{$lang->customers}</th>
                            <th >{$lang->country}</th>
                            <th >{$lang->endproduct}</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th >{$lang->brand}</th>
                            <th >{$lang->customers}</th>
                            <th >{$lang->country}</th>
                            <th >{$lang->endproduct}</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        {$brands_list}
                    </tbody>
                </table>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>