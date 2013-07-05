<head>
    <title>{$core->settings[systemtitle]} | {$lang->manageassets}</title>
    {$headerinc}
</head>
<body>
    {$header}
<tr>
    {$menu}
    <td class="contentContainer">
        <h3>{$lang->assign}</h3>
            <form name="perform_assets/assignassets_Form" id="perform_assets/assignassets_Form" enctype="multipart/form-data" method="post">
            <input type="hidden" name="auid" value="{$auid}" />
            <input type="hidden" value="do_{$actiontype}" name="action" id="action" />
        
                <div style="display:table; border-collapse:collapse; width:100%;">
                <div style="display:table-row;">
                    <div style="display:table-cell; width:10%;">{$lang->assigners}</div>
                    <div style="display:table-cell; width:100%; padding:5px;">{$employees_list}</div>
                </div>
                
                    <div style="display:table-row;">
                    <div style="display:table-cell; width:10%;">{$lang->assetid}</div>
                    <div style="display:table-cell; width:100%; padding:5px;">{$assets_list}</div>
                </div>

                <div style="display:table-row;">
                    <div style="display:table-cell; width:10%;">{$lang->from}</div>
                    <div style="display:table-cell; width:100%; padding:5px;"><input type="text" name="assignee[fromDate]"  value="{$assignee['fromDate_output']}"required="required" id="pickDateFrom" tabindex="3"/></div>
                </div>
                
                <div style="display:table-row;">
                    <div style="display:table-cell; width:10%;">{$lang->to}</div>
                    <div style="display:table-cell; width:100%; padding:5px;"><input type="text" name="assignee[toDate]"  value="{$assignee['toDate_output']}" required="required" id="pickDateTo" tabindex="4"/></div>
                </div>

                
                <div style="display:table-row;">
                    <div style="display: table-cell; width:20%;">
                        <input type="submit" class="button" value="{$actiontype}" id="perform_assets/assignassets_Button" />
                        <input type="reset" class="button" value="{$lang->reset}"/>
                    </div>
                </div>

                <div style="display:table-row">
                    <div style="display:table-cell;" id="perform_assets/assignassets_Results"></div>
                </div>
                </div>
    </td>

</tr>
{$footer}
</body>
</html>
