<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->managehighlights}</title>
        {$headerinc}
        <script>
            $(function () {
                $('#types').live('change', function () {
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
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="20%"><strong>{$lang->title}</strong></td>
                            <td width="25%"><input type='text' name='highlight[title]' value="{$highlight[title]}"></td>
                            <td width="5%"><strong>{$lang->type}</strong></td>
                            <td width="25%">{$types_list}</td>
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
                <input type="submit" value="{$lang->add}" id="perform_cms/managehighlight_Button" />
                <div id="perform_cms/managehighlight_Results"></div>
            </div>
        </td>
    </tr>
    {$footer}
</body>
</html>