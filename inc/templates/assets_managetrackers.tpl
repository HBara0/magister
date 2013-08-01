<html>
<head>
    <title>{$core->settings[systemtitle]} | {$lang->manageassets}</title>
    {$headerinc}

    <script lang="javascript">
        $(function() {
            $('.num').keydown(function(event) {
                // check for hyphen
                if ($(this).val().length >= 3) {
                    $(this).next('.num').focus();
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
        <h3>{$lang->createtracker}</h3>

        <form name="perform_assets/managetrackers_Form" id="perform_assets/managetrackers_Form" enctype="multipart/form-data" method="post">
            <input type="hidden" name="trackerid" value="{$trackerid}" />
            <input type="hidden" value="do_{$actiontype}" name="action" id="action" />
            <div style="display:inline-block; border-collapse:collapse; width:100%;">

                <div style="display:inline-block;">{$lang->IMEI}</div>
                <div style="display:inline-block; padding:10px;"><input type="text" size="30" name="tracker[IMEI]"  required="required" value="{$trackers[IMEI]}" tabindex="1"/></div>
                <div>
                    <div style="display:inline-block;">{$lang->deviceid}</div>
                    <div style="display:inline-block; padding:10px;"><input type="text" size="30" name="tracker[deviceId]"  required="required" value="{$trackers[deviceId]}" tabindex="2"/></div>
                </div>  

                <div>
                    <div style="display:inline-block;">{$lang->password}</div>
                    <div style="display:inline-block; padding:10px;"><input type="password" size="30" name="tracker[password]"  required="required" value="{$trackers[password]}" tabindex="3"/></div>
                </div>  


                <fieldset class="altrow" style="width:50%;"><legend class="subtitle">{$lang->siminfo} </legend>
                    <div>
                        <div style="display:inline-block;padding:5px;">  
                            {$lang->PUK}
                        </div>
                        <div style="display:inline-block;padding:5px;"> 

                            <input type="text" accept="numeric" size="7" name="tracker[PUK]"   value="{$trackers[PUK]}" tabindex="4"/>
                        </div>
                    </div>
                    <div>
                        <div style="display:inline-block;padding:5px;">  
                            {$lang->PIN}
                        </div>
                        <div style="display:inline-block;padding:5px; ">  
                            <input type="text" accept="numeric" size="7"  name="tracker[PIN]"  value="{$trackers[PIN]}" tabindex="5"/>
                        </div>
                    </div>
                    <div>
                        <div style="display:inline-block;padding:5px;">  
                            {$lang->phonenumber}
                        </div>
                        <div style="display:inline-block;padding:5px;">  
                            + <input type="text"  class="num" id="mobile_intcode" name="tracker[mobileintcode]" size="3" maxlength="3" accept="numeric" value="{$phonenumber_intcode}"/>
                            <input type="text"  class="num" id="mobile_areacode" name="tracker[mobileareacode]" size="4" maxlength="4" accept="numeric" value="{$phonenumber_areacode}"/>
                            <input type="text"   class="num" id="mobile_phonenumber"  name="tracker[Phonenumber]" value="{$trackers[Phonenumber]}" tabindex="6"/>
                        </div>
                    </div>

                </fieldset>



            </div>
            <div style="display:table-row;">
                <div style="display: table-cell; width:20%;">
                    <input type="submit" class="button" value="{$actiontype}" id="perform_assets/managetrackers_Button" />
                    <input type="reset" class="button" value="{$lang->reset}"/>
                </div>
            </div>

            <div style="display:table-row">
                <div style="display:table-cell;"id="perform_assets/managetrackers_Results"></div>
            </div>
        </div>
    </div>    

</form>

</td>

</tr>
{$footer}
</body>
</html>
