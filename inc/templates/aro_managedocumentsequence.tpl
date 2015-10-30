<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->aro}</title>
        {$headerinc}
        <script type="text/javascript">
            $(function() {
                $(document).on("change", 'input[id$=_approver]', function() {
                    var id = $(this).attr('id').split("_");
                    if($(this).val() != 'user') {
                        $("div[id^='user_" + id[1] + "']").hide();
                    }
                    $("div[id^='" + $(this).val() + "_" + id[1] + "']").effect("highlight", {color: "#D6EAAC"}, 1500).find("input").first().focus().val("");

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
            <form name="perform_aro/arodocumentsequeneconf_Form" id="perform_aro/arodocumentsequeneconf_Form"  action="#" method="post">
                <input type="hidden" id="wpid" name="documentsequence[adsid]" value="{$documentsequence[adsid]}">
                <table class="datatable"  style="width:100%;">
                    <tr><td>{$lang->affiliate} </td>
                        <td> {$affiliate_list}</td>
                    </tr>
                    <tr><td>{$lang->purchasetype}  </td>
                        <td>{$purchasetypelist} </td>
                    </tr>
                    <tr ><td>{$lang->effromdate} </td>
                        <td> <input type="text" id="pickDate_from"  autocomplete="off" tabindex="2" value="{$documentsequence[effectiveFrom_output]}" required="required" /> </td>               </td>

                        <td> <input type="hidden" name="documentsequence[effectiveFrom]" id="altpickDate_from" value="{$documentsequence[effectiveFrom_formatted]}"/></td>
                    </tr>
                    <tr><td>{$lang->eftodate}  </td>
                        <td> <input type="text" id="pickDate_to" autocomplete="off" tabindex="2" value="{$documentsequence[effectiveTo_output]}" required="required" />  </td>
                        <td> <input type="hidden" name="documentsequence[effectiveTo]" id="altpickDate_to" value="{$documentsequence[effectiveTo_formatted]}"/></td>

                    </tr>

                    <tr><td>{$lang->prefix}  </td>
                        <td> <input type="text"   autocomplete="off" tabindex="2" name="documentsequence[prefix]" value="{$documentsequence[prefix]}" />  </td>
                    </tr>
                    <tr><td>{$lang->incrementby}  </td>
                        <td> <input type="number" step="1" min="1"   autocomplete="off" tabindex="2"  name="documentsequence[incrementBy]" value="{$documentsequence[incrementBy]}" />  </td>
                    </tr>
                    <tr><td>{$lang->nextnumber}  </td>
                        <td> <input type="number" step="1" min="1"  autocomplete="off" tabindex="2"  name="documentsequence[nextNumber]" value="{$documentsequence[nextNumber]}" />  </td>
                    </tr>
                    <tr><td>{$lang->suffix}  </td>
                        <td> <input type="text"    autocomplete="off" tabindex="2"  name="documentsequence[suffix]" value="{$documentsequence[suffix]}" />  </td>
                    </tr>
                    <tr>
                </table>
                <input type="submit" id="perform_aro/arodocumentsequeneconf_Button" value="Save" class="button"/>
            </form>
            <div id="perform_aro/arodocumentsequeneconf_Results"></div>
        </td>
    </tr>
</body>
</html>