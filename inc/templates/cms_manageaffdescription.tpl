<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->manageaffdesc}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->manageaffdesc}</h1>
            <div>
                <form action="#" method="post" id="perform_cms/manageaffdesc_Form" name="perform_cms/manageaffdesc_Form">
                    <input type='hidden' name='affid' value="{$affiliate->affid}">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width:10%;"><strong>{$lang->affiliate}</strong></td>
                            <td>{$affiliate->parse_link()}</td>
                        </tr>
                        <tr>
                            <td style="width:10%;vertical-align: top"><strong>{$lang->description}</strong></td>
                            <td>  <div style="display:block;"><textarea name="description" cols="100" rows="6" id='description' class="txteditadv">{$affiliate->description}</textarea></td>
                        </tr>
                    </table>
                </form>
                <hr />
                <input type="submit" class="button" value="{$lang->savecaps}" id="perform_cms/manageaffdesc_Button" />
                <div id="perform_cms/manageaffdesc_Results"></div>
            </div>
        </td>
    </tr>
    {$footer}
</body>
</html>