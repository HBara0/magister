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
                        sharedFunctions.populateForm('perform_aro/managearodouments_Form', 'http://127.0.0.1/ocos/index.php?module=aro/managearodouments&action=populatedocnum&affid= ' + affid + '&ptid= ' + ptid);
                    }
                });
                $("#currencies").live('change', function() {
                    sharedFunctions.populateForm('perform_aro/managearodouments_Form', 'http://127.0.0.1/ocos/index.php?module=aro/managearodouments&action=getexchangerate&currency=' + $(this).val());
                });
                $("select[id^='paymentermdays_']").live('change', function() {
                    var id = $(this).attr('id').split('_');
                    var avgesdateofsale = '11-02-2015';
                    var parentContainer = $(this).closest('div');
                    var paymentdays = [];
                    parentContainer.children('table').find('tr').each(function() {
                        /*check if the customer is selected */
                        if($(this).find("input[id^='customer_']").val() != '') {
                            $(this).find('select').each(function() {
                                if($(this).val() != '') {
                                    paymentdays.push($(this).val());
                                }
                            });
                        }
                    });

                    alert(paymentdays);
                    var purchasetype = $("input[id^='cpurchasetype']").val();

                    sharedFunctions.populateForm('perform_aro/managearodouments_Form', 'http://127.0.0.1/ocos/index.php?module=aro/managearodouments&action=getestimatedate&avgesdateofsale= ' + avgesdateofsale + '&paymentermdays[]= ' + paymentdays + '&ptid= ' + purchasetype);
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