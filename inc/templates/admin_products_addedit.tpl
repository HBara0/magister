<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$pagetitle}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
        <script type='text/javascript'>
            $(function() {
                //Keep track of last scroll
                var lastScroll = 0;
                $("#chemicalsubstances").scroll(function(event) {
                    //Sets the current scroll position

                    if ($(this).scrollTop() > $(this).offset().top) {
                        if (sharedFunctions.checkSession() == false) {
                            return;
                        }
                        sharedFunctions.requestAjax("post", "index.php?module=products/add&action=getmorechem", "uid=" + $('#uid').val(), 'to_inform_fields', 'to_inform_fields', true);

                    }
                });
            });
        </script>
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$pagetitle}</h3>
            <form id="perform_products/{$actiontype}_Form" name="perform_products/{$actiontype}_Form" action="#" method="post">
                {$pidfield}
                <table cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>{$lang->code}</td><td><input type="text" name="code" id="code" tabindex="1" value="{$product[code]}"/></td>

                    </tr>
                    <tr>
                        <td><strong>{$lang->name}</strong></td><td><input type="text" name="name" id="name" tabindex="2" value="{$product[name]}"/></td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->generic}</strong></td><td>{$generics_list}</td>
                    </tr>
                    <tr>    
                        <td><strong>{$lang->supplier}</strong></td><td><input type='text' id='supplier_1_QSearch' value="{$product[suppliername]}"/><input type="text" size="3" id="supplier_1_id_output" value="{$product[spid]}" disabled/><input type='hidden' id='supplier_1_id' name='spid' value="{$product[spid]}" /><div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div></td>
                    </tr>



                    <tr class="thead"> <td  colspan="2">{$lang->manageapplicationsfunc}..</td></tr>
                    <tr>
                        <td colspan="2">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tbody id="segmentsapplications_tbody">
                                    <tr id="1">
                                        <td colspan="2">  
                                            <div style="width:100% ;height:340px; overflow:auto; display:inline-block; vertical-align:top;">
                                                <table class="datatable" width="100%">
                                                    <tr class="altrow2">
                                                        <th>{$lang->isdefault}</th>
                                                        <th>&nbsp;</th>
                                                        <th>{$lang->functions}</th>
                                                        <th>{$lang->applications}</th>    
                                                        <th>{$lang->segment}</th>
                                                    </tr>
                                                    {$admin_products_addedit_segmentsapplicationsfunctions_rows} 

                                                </table>
                                            </div> 
                                        </td>
                                    </tr>
                                </tbody>

                            </table>
                        </td>
                    </tr>
                    <tr class="thead"> <td  colspan="2">{$lang->chemicalsubstances}..</td></tr>
                    <tr>
                        <td colspan="2">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tbody id="chemicalslist_tbody">
                                    <tr id="1">
                                        <td colspan="2">  
                                            <div  id="chemicalsubstances" style="width:100% ;height:140px; overflow:auto; display:inline-block; vertical-align:top;">
                                                <table class="datatable" width="100%">
                                                    <tr class="altrow2">
                                                        <th>&nbsp;</th>
                                                        <th>{$lang->casnum}</th>
                                                        <th>{$lang->checmicalproduct}</th>
                                                        <th>{$lang->synonyms}</th>                                                
                                                    </tr>
                                                    {$chemicalslist_section}
                                                </table>
                                            </div> 
                                        </td>
                                    </tr>
                                </tbody>

                            </table>
                        </td>
                    </tr>


                    <tr>

                        <td>{$lang->description}</td><td><textarea cols="30" rows="5" id="description" name="description" tabindex="5">{$product[description]}</textarea></td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->defaultcurrency}</strong></td><td><select name="defaultCurrency" id="defaultCurrency" tabindex="6" disabled><option value="USD" selected="selected">USD</option><option value="EURO">EURO</option></select></td>
                    </tr>
                    <tr>
                        <td>{$lang->taxrate}</td><td><input type="text" name="taxRate" id="taxRate" tabindex="7" disabled/></td>
                    </tr>
                    <tr>

                        <td>{$lang->package}</td><td><input type="text" name="package" id="package" tabindex="8" disabled/></td>
                    </tr>
                    <tr>
                        <td>{$lang->itemweightmt}</td><td><input type="text" name="itemWeight" id="itemWeight" tabindex="9" disabled/></td>
                    </tr>
                    <tr>
                        <td>{$lang->standard}</td><td><input type="text" name="standard" id="standard" tabindex="10" disabled/></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="button" value="{$lang->$actiontype}" id="perform_products/{$actiontype}_Button" /> <input type="reset" value="{$lang->reset}" />
                            <div id="perform_products/{$actiontype}_Results"></div>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>