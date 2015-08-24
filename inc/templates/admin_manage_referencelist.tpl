<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->managereferencelist}</title>
        {$headerinc}
        <script>
            $(function () {
                $(document).on('change', "select[id='selectlist[referenceType]']", function () {
                    if($(this).val() == 'list') {
                        $("div[id='lines']").fadeIn();
                        $("div[id='table']").hide();

                    }
                    else if($(this).val() == 'table') {
                        $("div[id='table']").fadeIn();
                        $("div[id='lines']").hide();
                    }
                });
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>{$lang->managereferencelist}{$list_name}</h1>
            <form name="perform_managesystem/managereferenecelist_Form" id="perform_managesystem/managereferencelist_Form" action="#" method="post">
                <div>
                    <table>
                        <tr>
                            <td>{$lang->name}</td><td><input required="required"  type='text' name='selectlist[name]' value="{$selectlist['name']}">
                                <input type="hidden" name="selectlist[srlid]" value="{$srlid}">
                            </td>
                            <td>{$lang->referencetype}</td><td>{$referencetype_select}</td>
                            <td>{$lang->selecttype}</td><td>{$selecttype_select}</td>
                        </tr>
                        <tr>
                    </table>
                    {$referenece_lines}
                </div>
                <div style="display:inline-block;"><input type="submit" class="button" value="{$lang->save}" id="perform_managesystem/managereferencelist_Button"/>
            </form>
            <div style="display: inline-block" id="perform_managesystem/managereferencelist_Results"></div>
        </td>
    </tr>
    {$footer}
</body>
</html>