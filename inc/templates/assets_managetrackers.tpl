<head>
    <title>{$core->settings[systemtitle]} | {$lang->manageassets}</title>
    {$headerinc}
</head>
<body>
    {$header}
<tr>
    {$menu}
    <td class="contentContainer">
        <h3>{$actiontype}</h3>

        <form name="perform_assets/managetrackers_Form" id="perform_assets/managetrackers_Form" enctype="multipart/form-data" method="post">
            <input type="hidden" name="asid" />
            <input type="hidden" value="do_{$actiontype}" name="action" id="action" />
            <div style="display:table; border-collapse:collapse; width:100%;">
                <div style="display:table-row;">
                    <div style="display:table-cell; width:10%;">{$lang->deviceId}</div>
                    <div style="display:table-cell; width:100%; padding:5px;"><input type="text" name="tracker[deviceId]" value="{$trackers['deviceId']}" tabindex="1"/></div>
                </div>
                
                    <div style="display:table-row;">
                    <div style="display:table-cell; width:10%;">{$lang->assetid}</div>
                    <div style="display:table-cell; width:100%; padding:5px;">{$assetslist}</div>
                </div>
                
                    <div style="display:table-row;">
                    <div style="display:table-cell; width:10%;">{$lang->from}</div>
                    <div style="display:table-cell; width:100%; padding:5px;"><input type="text" name="tracker[fromDate]" id="pickDateFrom" tabindex="3"/></div>
                </div>
                
                      <div style="display:table-row;">
                    <div style="display:table-cell; width:10%;">{$lang->to}</div>
                    <div style="display:table-cell; width:100%; padding:5px;"><input type="text" name="tracker[toDate]" id="pickDateTo" tabindex="4"/></div>
                </div>
                    
                 
                    
                          <div style="display:table-row;">
                    <div style="display: table-cell; width:20%;">
                        <input type="button" class="button" value="{$actiontype}" id="perform_assets/managetrackers_Button" />
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
