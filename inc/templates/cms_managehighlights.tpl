<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->managehighlights}</title>
        {$headerinc}
        <script>
            $(function () {
                $(document).on('change', '#types', function () {
                    if($(this).val() == 'graph') {
                        $('#type_html').hide();
                        $('#type_graph').show();
                    }
                    else if($(this).val() == 'html') {
                        $('#type_html').show();
                        $('#type_graph').hide();
                    }
                });
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->managehighlights}</h1>
            <div>

                <form action="#" method="post" id="perform_cms/managehighlight_Form" name="perform_cms/managehighlight_Form">
                    <input type='hidden' name='highlight[cmshid]' value="{$highlight['cmshid']}">
                    <input type='hidden' name='actiontype' value="{$action}">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="20%"><strong>{$lang->title}</strong></td>
                            <td width="25%"><input type='text' name='highlight[title]' value="{$highlight[title]}"></td>
                            <td width="5%"><strong>{$lang->type}</strong></td>
                            <td width="25%">{$types_list}</td>
                            <td width="10%">{$lang->isenabled}</td>
                            <td><input type='checkbox' value='1' name='highlight[isEnabled]' {$enablecheck}></td>
                        </tr>
                        <tr>
                            {$type_html}
                        </tr>
                        <tr>
                            {$type_graph}
                        </tr>
                    </table>
                </form>
                <hr />
                <input type="submit" class="button" value="{$lang->savecaps}" id="perform_cms/managehighlight_Button" />
                <div id="perform_cms/managehighlight_Results"></div>
            </div>
        </td>
    </tr>
    {$footer}
</body>
</html>