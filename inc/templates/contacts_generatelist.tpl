<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->generatecontactlist}</title>
        {$headerinc}
        <script type="text/javascript" src="{$core->settings[rootdir]}/js/tableExport.min.js"></script>
        <script type="text/javascript" src="{$core->settings[rootdir]}/js/jquery.base64.min.js"></script>
        <script>
            $(function () {
                var icons = {
                    header: "ui-icon-circle-arrow-e",
                    activeHeader: "ui-icon-circle-arrow-s"
                };
                $('#accordion').accordion(
                        {
                            icons: icons,
                            heightStyle: "content",
                            activate: function (event, ui) {
                                ui.newHeader.find('input').prop('checked', true)
                            }
                        });
                $('input[type=radio]').on('click', function (e) {
                    e.stopPropagation();
                }
                );
                $(document).on('change', 'input[id^="button"]', function () {
                    if($('#button_user').is(':checked')) {
                        $('#div_user').find('input').removeAttr('disabled');
                        $('#div_user').find('select').removeAttr('disabled');
                    }
                    else {
                        $('#div_user').find('input').prop('disabled', 'disabled');
                        $('#div_user').find('select').prop('disabled', 'disabled');
                    }
                    if($('#button_rep').is(':checked')) {
                        $('#div_rep').find('input').removeAttr('disabled');
                        $('#div_rep').find('select').removeAttr('disabled');
                    }
                    else {
                        $('#div_rep').find('input').prop('disabled', 'disabled');
                        $('#div_rep').find('select').prop('disabled', 'disabled');
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
            <h1>{$lang->generatecontactlist}</h1>
            <form name="perform_contactcenter/generatelist_Form" id="perform_contactcenter/generatelist_Form" action="#" method="post">
                <div id="accordion">
                    <h3><input id="button_user" name="action" value="user"  type="radio" {$userchecked}>{$lang->employee}</h3>
                    <div id="div_user">
                        <table  width="100%" border="0" cellspacing="0" cellpadding="2">
                            <thead>
                                <tr>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->name}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->position}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->assignedbusinesspartner}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->segments}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->mainaffiliate}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->assignedaffiliate}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->reportsto}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    {$filters_user_row}
                                </tr>
                                <tr>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->clicktoshowfield}</td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"><input type="checkbox" name="user[]" value="position"></td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"><input type="checkbox" name="user[]" value="entities"></td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"><input type="checkbox" name="user[]" value="segment"></td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"><input type="checkbox" name="user[]" value="allenabledaffiliates"></td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"><input type="checkbox" name="user[]" value="allaffiliates"></td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"><input type="checkbox" name="user[]" value="reportsTo"></td>
                                </tr>
                                <tr></tr>
                            </tbody>
                        </table>
                    </div>
                    <h3><input id="button_rep" name="action" value="rep" type="radio" {$repchecked}>{$lang->representatives}</h3>
                    <div id="div_rep">
                        <table width="100%" border="0" cellspacing="0" cellpadding="2">
                            <thead>
                                <tr>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->name}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->businesspartner}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->companytype}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->suppliertype}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->segments}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->assignedaffiliate}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->requiresqr}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->hascontract}</th>
                                    <th width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->country}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    {$filters_repr_row}
                                </tr>
                                <tr>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center">{$lang->clicktoshowfield}</td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"></td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"><input type="checkbox" name="representative[]" value="companytype"></td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"><input type="checkbox" name="representative[]" value="suppliertype"></td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"><input type="checkbox" name="representative[]" value="segment"></td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"><input type="checkbox" name="representative[]" value="assignedaff"></td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"><input type="checkbox" name="representative[]" value="requiresQr"></td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"><input type="checkbox" name="representative[]" value="hasContract"></td>
                                    <td width="250px" class=" border_right" rowspan="2" valign="top" align="center"><input type="checkbox" name="representative[]" value="coid"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <input type='submit' style="cursor: pointer" class='button main' value="{$lang->generate}" id='perform_contactcenter/generatelist_Button'>
                <input type="reset" value="{$lang->reset}" class='button'>
            </form>
            <hr>
            <div id="perform_contactcenter/generatelist_Results"></div>
        </td>
    </tr>
    {$footer}
</body>
</html>