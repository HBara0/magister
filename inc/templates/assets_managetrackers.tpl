<head>
    <title>{$core->settings[systemtitle]} | {$lang->manageassets}</title>
    {$headerinc}
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
                    <div style="display:inline-block; padding:10px;"><input type="text" size="30" name="tracker[IMEI]"  required="required" value="{$trackers['IMEI']}" tabindex="1"/></div>
           

               
                        <fieldset class="altrow" style="width:50%;"><legend class="subtitle">{$lang->siminfo} </legend>
                         <div >
                                <div style="display:inline-block;padding:5px;">  
                                    {$lang->PUK}
                                </div>
                                <div style="display:inline-block;padding:5px;"> 

                                    <input type="text" accept="numeric" size="7" name="tracker[PUK]"   value="{$trackers[PUK]}" tabindex="1"/>
                                </div>
                             </div>
                                <div>
                                <div style="display:inline-block;padding:5px;">  
                                    {$lang->PIN}
                                </div>
                                <div style="display:inline-block;padding:5px; ">  
                                    <input type="text" accept="numeric" size="7"  name="tracker[PIN]"  value="{$trackers[PIN]}" tabindex="1"/>
                                </div>
                             </div>
                            <div>
                                <div style="display:inline-block;padding:5px;">  
                                    {$lang->phonenumber}
                                </div>
                                <div style="display:inline-block;padding:5px;">  
                                    <input type="text"   name="tracker[Phonenumber]" value="{$trackers[Phonenumber]}" tabindex="1"/>
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
