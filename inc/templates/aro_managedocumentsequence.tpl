<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->aro}</title>
        {$headerinc}
        <script type="text/javascript">
            $(function() {
                $(document).on("change", 'input[id=documentsequence_nextNumber],input[id=documentsequence_suffix],input[id=documentsequence_prefix]', function() {
                    if(typeof $("input[id='documentsequence_nextNumber']").val() != 'undefined' && typeof $("input[id='documentsequence_suffix']").val() != 'undefined'
                            && typeof $("input[id='documentsequence_prefix']").val() != 'undefined') {

                        if($("input[id='documentsequence_nextNumber']").val().length > 0 && $("input[id='documentsequence_suffix']").val().length > 0
                                && $("input[id='documentsequence_prefix']").val().length > 0) {

                            var value = $("input[id='documentsequence_prefix']").val() + '-' + $("input[id='documentsequence_nextNumber']").val() + '-' + $("input[id='documentsequence_suffix']").val();
                            var nextvalue = $("input[id='documentsequence_prefix']").val() + '-' + (parseInt($("input[id='documentsequence_nextNumber']").val()) + 1) + '-' + $("input[id='documentsequence_suffix']").val();
                            $("div[id='example']").effect("highlight", {color: "#D6EAAC"}, 1500).html('<span style="font-weight:bold;">{$lang->current} {$lang->orderreference}: </span>' + value + '<br/><span style="font-weight:bold;">{$lang->next} {$lang->orderreference}: </span>' + nextvalue);
                        }
                    }
                });
            });
        </script>
    </head>
    <body>
    <tr>
        {$header}
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>{$lang->managedoumentsequence}</h1>
            <div>{$lang->documnetconfigurationdesc}</div><br/>

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
                    <tr><td>{$lang->prefix}</td>
                        <td> <input type="text"   autocomplete="off" tabindex="2" name="documentsequence[prefix]" value="{$documentsequence[prefix]}" id="documentsequence_prefix"/>  </td>
                    </tr>
                    <tr><td>{$lang->incrementby}  </td>
                        <td> <input type="number" step="1" min="1"   autocomplete="off" tabindex="2"  name="documentsequence[incrementBy]" value="{$documentsequence[incrementBy]}" />  </td>
                    </tr>
                    <tr><td>{$lang->nextnumber}<a title="{$lang->nextnumtooltip}" href="#"><img src="./images/icons/question.gif"></a></td>
                        <td> <input type="number" step="1" min="1" autocomplete="off" tabindex="2"  name="documentsequence[nextNumber]" value="{$documentsequence[nextNumber]}" id="documentsequence_nextNumber"/>  </td>
                    </tr>
                    <tr><td>{$lang->suffix}</td>
                        <td> <input type="text" autocomplete="off" tabindex="2"  name="documentsequence[suffix]" value="{$documentsequence[suffix]}" id="documentsequence_suffix" />  </td>
                    </tr>
                </table>
                <br/>
                <div class="altrow2" id="example" style="border:black solid 1px;padding: 5px;width:25%">
                    <span style="font-weight: bold;">{$lang->current} {$lang->orderreference}:</span> {$lang->orderrefernceformat}<br/>
                    <span style="font-weight: bold;">{$lang->next} {$lang->orderreference}:</span> {$lang->nextorderrefernceformat}
                </div><br/>
                <input type="submit" id="perform_aro/arodocumentsequeneconf_Button" value="Save" class="button"/>
            </form>
            <div id="perform_aro/arodocumentsequeneconf_Results"></div>
        </td>
    </tr>
</body>
</html>