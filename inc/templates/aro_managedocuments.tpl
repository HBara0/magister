<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->aro}</title>
        {$headerinc}
        <script type="text/javascript">
            $(function() {
                $('.accordion .header').accordion({collapsible: true});
                $('.accordion .header').click(function() {

                    $(this).next().toggle();

                    return false;
                }).next().hide();

                //  var json2 = "{'orderreference':'Dave Stewart'}";
                //  var json2 = eval("(" + json2 + ");"); /* convert the json to object */
                // var form = document.forms['perform_aro/managearodouments_Form'];
                // $(form).populate(json2, {debug: 0})


                $("#affid,#purchasetype").live('change', function() {
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }
                    $(this).data('affid', $('select[id=affid]').val());
                    var affid = $(this).data('affid')
                    $(this).data('purchasetype', $('select[id=purchasetype]').val());
                    var ptid = $(this).data('purchasetype')
                    if(ptid !== '' && ptid != typeof undefined) {
                        sharedFunctions.populateForm('perform_aro/managearodouments_Form', 'http://127.0.0.1/ocos/index.php?module=aro/managearodouments&action=populate_documentpattern&affid= ' + affid + '&ptid= ' + ptid);
                    }
                });
                $("#currencies").live('change', function() {
                    sharedFunctions.populateForm('perform_aro/managearodouments_Form', 'http://127.0.0.1/ocos/index.php?module=aro/managearodouments&action=getexchangerate&currency=' + $(this).val());

                });

            });
        </script>
    </head>
    <body>
    <tr>
        {$header}
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>{$lang->managedoumentsequence} </h1>

            <div class="accordion">
                <form name="perform_aro/managearodouments_Form" id="perform_aro/managearodouments_Form"  action="#" method="post">
                    {$aro_managedocuments_orderident}
                    {$aro_ordercustomers}

                    <input type="submit" class="button" id="perform_aro/managearodouments_Button" value="{$lang->save}"/>
                </form>
                <div id="perform_aro/managearodouments_Results"></div>
            </div>

        </td>
    </tr>
</body>
</html>