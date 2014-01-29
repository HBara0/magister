<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->addaproduct}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->addaproduct}</h3>
            <form id="perform_contents/addproducts_Form" name="perform_contents/addproducts_Form"  method="post">
                <table cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td>{$lang->code}</td><td><input type="text" name="code" id="code" tabindex="1"/></td>

                    </tr>
                    <tr>
                        <td><strong>{$lang->name}</strong></td><td><input type="text" name="name" id="name" tabindex="2" required="required" /></td>
                    </tr>
                    <tr>
                        <td><strong>{$lang->generic}</strong></td><td>{$generics_list}</td>
                    </tr>
                    <tr>    
                        <td><strong>{$lang->supplier}</strong></td><td><input type='text' required="required" id='supplier_1_QSearch' /><input type="text" size="3" id="supplier_1_id_output" disabled /><input type='hidden' value='' id='supplier_1_id' name='spid' /><div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div></td>
                    </tr>
                    <tr>

                        <td>{$lang->description}</td><td><textarea cols="30" rows="5" id="description" name="description" tabindex="5"></textarea></td>
                    </tr>
                    <tr class="thead"> <td  colspan="2">Manage applications and Functions..</td></tr>
                    <tr id="internalinvitations_row">
                        <td colspan="2">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tbody id="invitationsgroup_tbody">
                                    <tr id="1">
                                        <td colspan="2">  
                                            <div style="width:100% ;height:340px; overflow:auto; display:inline-block; vertical-align:top;">
                                                <table class="datatable" width="100%">
                                                    <tr class="altrow2">
                                                        <th>&nbsp;</th>
                                                        <th>{$lang->functions}</th>
                                                        <th>{$lang->isdefault}</th>
                                                        <th>{$lang->applications}</th>                                                
                                                        <th>{$lang->segment}</th>
                                                    </tr>
                                                    {$contents_products_add_segmentsapplicationsfunctions} 

                                                </table>
                                            </div> 
                                        </td>
                                    </tr>
                                </tbody>

                            </table>
                        </td>
                    </tr>



                    <tr>
                        <td><strong>{$lang->defaultcurrency}</strong></td><td><select name="defaultCurrency" id="defaultCurrency" required="required" tabindex="6" disabled><option value="USD" selected="selected">USD</option><option value="EURO">EURO</option></select></td>
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
                        <td colspan="2"><input type="submit" value="{$lang->add}" id="perform_contents/addproducts_Button" /> <input type="reset" value="{$lang->reset}" />
                            <div id="perform_contents/addproducts_Results"></div>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>