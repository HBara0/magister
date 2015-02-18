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
                        sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populatedocnum&affid= ' + affid + '&ptid= ' + ptid);
                    }
                });
                $("#currencies").live('change', function() {
                    sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=getexchangerate&currency=' + $(this).val());
                });
                //   $("input[id$='_qtyPotentiallySold'],input[id$='_quantity']").live('change', function() {

                $("input[id$='_qtyPotentiallySoldPerc']").live('change', function() {
                    var id = $(this).attr('id').split("_");
                    var fields = '';
                    fields = '&' + id[2] + '=' + $("input[id='productline_" + id[1] + "_" + id[2] + "']").val();
                    var quantity = $("input[id='productline_" + id[1] + "_quantity']").val();
                    sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateproductlinefields&rowid=' + id[1] + fields + '&quantity=' + quantity);
                });


                $("input[id^='productline_']").live('change', function() {
                    var id = $(this).attr('id').split("_");
                    var fields_array = ["quantity", "qtyPotentiallySold", "intialPrice", "costPrice"];
                    var fields = '';
                    $.each(fields_array, function(index, value) {
                        fields += '&' + value + '=' + $("input[id='productline_" + id[1] + "_" + value + "']").val();
                    });
                    var ptid = $("#purchasetype").val();
                    sharedFunctions.populateForm('perform_aro/managearodouments_Form', rootdir + 'index.php?module=aro/managearodouments&action=populateproductlinefields&rowid=' + id[1] + '&ptid=' + ptid + fields);
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
                    {$aro_productlines}
                    <input type="submit" class="button" id="perform_aro/managearodouments_Button" value="{$lang->save}"/>
                </form>
                <div id="perform_aro/managearodouments_Results"></div>
            </div>

        </td>
    </tr>
</body>
</html>