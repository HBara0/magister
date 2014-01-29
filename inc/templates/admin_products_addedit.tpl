<html>
<head>
<title>{$core->settings[systemtitle]} | {$pagetitle}</title>
{$headerinc}
</head>
<body>
{$header}
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
<tr>

                                <tody id="chemicalslist_tbody">
                                        <td colspan="0">  
                                            <div  id="chemicalsubstances" style="width:100% ;height:200px; overflow:auto; display:inline-block; vertical-align:top;">

                                                    <tbody id="chemicalproduct_tbody" >
                                                        {$chemrows}
                                                   
                                                    </tbody>
                                                    <tr><td colspan="0"> <img src="../images/add.gif" id="addmore_chemicalproduct" alt="{$lang->add}" title="{$lang->add}" />
                                                        </td></tr>
                                    </tbody>
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
<div id="popup_createchemical" title="{$lang->createchemical}">
    <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p>{$lang->createchemical_notes}</p></div>
    <form name='add_chemical_products/add_Form' id='add_chemical_products/add_Form' method="post">
        <input type="hidden" id="action" name="action" value="do_createchemical" />
        <div style="display:table-row">
            <div style="display:table-cell; width:100px; vertical-align:middle; font-weight:bold;">{$lang->casnum}</div>
            <div style="display:table-cell; padding:3px">
                <input name="chemcialsubstances[casNum]" type="text" />
            </div>
        </div>
        <div style="display:table-row">
            <div style="display:table-cell; font-weight:bold;">{$lang->chemicalname}</div>
            <div style="display:table-cell; padding:3px" >
                <input name="chemcialsubstances[name]" size="40" type="text" />
            </div>
        </div>
        <div style="display:table-row">
            <div style="display:table-cell; vertical-align:top;">{$lang->chemicalsynonym}</div>
            <div style="display:table-cell;padding:3px">
                <textarea  name="chemcialsubstances[synonyms]" cols="40" rows="5"></textarea>
                <div class="smalltext">{$lang->synonymnotes}</div>
            </div>
        </div>
        <hr />
        <div style="display:table-row">
            <div style="display:table-cell">
                <input type="button" id="add_chemical_products/add_Button" class="button" value="{$lang->add}"/>
                <input type="reset" class="button" value="{$lang->reset}" />
            </div>
        </div>
    </form>
    <div id="add_chemical_products/add_Results"></div>
</div>
</body>
</html>