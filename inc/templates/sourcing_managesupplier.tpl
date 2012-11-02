<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->modifysitesettings}</title>
{$headerinc}

</head>
<body>
{$header}
<script>

$(function(){
	$("#companyName").on('change',function() {
		var name = $("#companyName").val();
		$.post("index.php?module=sourcing/managesupplier&action=checkcompany", { company: name}, function(returnedData) {
		
		if(returnedData) {
			$("#companyresult").text('Company name Already exist: '+returnedData).css("color","red");
			 $('input[type="button"]').attr("disabled", "true");
			}
			else
			{
				$('input[type="button"]').removeAttr('disabled');
				$("#companyresult").empty();
			}
		});		
	
		})

});
</script>
<tr>
{$menu}
    <td class="contentContainer">
    <h3>{$actiontype}</h3>

  <form   name="perform_sourcing/managesupplier_Form" action="{$_SERVER[QUERY_STRING]}" method="post"  id="perform_sourcing/managesupplier_Form" >
    <input class="rounded" type="hidden" value="do_{$actiontype}page" name="action" id="action" />
     <input name="supplier[ssid]" type="hidden" value="{$supplierid}">
    <div  class="thead" style="paddinng:5px;">{$lang->generalinfo}</div>
    <div style="display:table-row;">
    <div style="display: table-cell; width:100px;"><strong>{$lang->companyname}</strong></div>
    <div style="display: table-cell; padding:5px; ">
   	 	<input class="rounded"   id="companyName" name="supplier[companyName]" type="text" value="{$supplier[companyName]}" size="30" >
       
        </div>
        <div style="display: table-cell; padding:0px; ">{$lang->abbreviation}</div>
            <div style="display: table-cell; padding:5px; ">
    <input class="rounded"  name="supplier[companyNameAbbr]" type="text" value="{$supplier[companyNameAbbr]}" size="15">
     </div>
    <div id="companyresult" style="display: table-cell; paddinng:5px; "></div>
</div>

      <div style="display:table-row;">
    <div style="display: table-cell; width:0px; vertical-align:middle;">{$lang->product}</div>
    <div style="display: table-cell; padding:5px; "><select name="supplier[type]">
    <option value="trader">{$lang->trader}</option>
    <option value="producer">{$lang->producer}</option>
    </select></div>
    </div>
    
     <div style="display:table-row;">
    <div style="display: table-cell; width:0px; vertical-align:middle;">{$lang->product}</div>
    <div style="display: table-cell; padding:5px; ">{$product_list}</div>
    </div>
           <div style="display:table-row;">
    <div style="display: table-cell; width:0px;vertical-align:middle;">{$lang->activityarea}</div>
    <div style="display: table-cell; padding:5px; ">{$activityarea_list}</div>
    </div>
    
       <div style="display:table-row;">
    <div style="display: table-cell; width:0px;">{$lang->country}</div>
    <div style="display: table-cell; padding:5px; ">{$countries_list}</div>
    </div>
    <div style="display:table-row;">
    <div style="display: table-cell; width:0px;">{$lang->city}</div>
    <div style="display: table-cell; padding:5px; "><input class="rounded" type="text" id="city"  value="{$supplier[city]}" name="supplier[city]" /></div>
   </div>
      
    <div style="display:table-row;">
    <div style="display: table-cell; width:0px;">{$lang->address1}</div>
    <div style="display: table-cell; padding:5px; ">
    <input class="rounded" type="text" value="{$supplier[addressLine1]}" name="supplier[addressLine1]" /> </div>
     </div> 
     
    <div style="display:table-row;">
    <div style="display: table-cell; width:0px;">{$lang->address2}</div>
    <div style="display: table-cell; padding:5px; ">
    <input class="rounded" type="text"   value="{$supplier[addressLine2]}" name="supplier[addressLine2]" />
    </div>
   </div>
   
    <div style="display:table-row;">
    <div style="display: table-cell; width:0px;">{$lang->buildingname}</div>
    <div style="display: table-cell; padding:5px; ">
    <input class="rounded" type="text"  value="{$supplier[building]}" name="supplier[building]" />
    </div>
   </div>

    <div style="display:table-row;">
    <div style="display: table-cell; width:0px;">{$lang->postcode}</div>
    <div style="display: table-cell; padding:5px; ">
    <input class="rounded" type="text"   value="{$supplier[postCode]}" name="supplier[postCode]" />
    </div>
   </div>

    <div style="display:table-row;">
    <div style="display: table-cell; width:0px;">{$lang->pobox}</div>
    <div style="display: table-cell; padding:5px; ">
    <input class="rounded" size="10"type="text"  value="{$supplier[poBox]}" name="supplier[poBox]" />
    </div>
   </div>

    <div style="display:table-row;">
    <div style="display: table-cell; width:0px;">{$lang->phone1}</div>
    <div style="display: table-cell; padding:5px; ">
     + <input type="text" class="rounded" id="phone1_intcode" name="supplier[phone1]_intcode" size="3" maxlength="3" accept="numeric" />
     <input type="text" class="rounded" id="phone1_areacode" name="supplier[phone1]_areacode" size='4' maxlength="4" accept="numeric" />
     <input type="text" class="rounded" id="phone1_number"name="supplier[phone1]"  value="{$supplier[phone1]}" accept="numeric"  />
     <br />
    </div>
   </div>
  
     <div style="display:table-row;">
    <div style="display: table-cell; width:0px;">{$lang->phone2}</div>
    <div style="display: table-cell; padding:5px; ">
     + <input type="text" class="rounded" id="phone2_intcode" name="supplier[phone2]_intcode" size="3" maxlength="3" accept="numeric" />
     <input type="text" class="rounded"id="phone2_areacode" name="supplier[phone2]_areacode" size='4' maxlength="4" accept="numeric" />
      <input type="text"  class="rounded"id="phone2_number"name="supplier[phone2]" value="{$supplier[phone2]}" accept="numeric"  /><br />
    </div>
   </div>
       <div style="display:table-row;">
    <div style="display: table-cell; width:0px;">{$lang->fax}</div>
    <div style="display: table-cell; padding:5px; ">
     + <input type="text" class="rounded" id="fax_intcode" name="supplier[fax]_intcode" size="3" maxlength="3" accept="numeric" />
     <input type="text" class="rounded" id="fax_areacode" name="supplier[fax]_areacode" size='4' maxlength="4" accept="numeric" />
      <input type="text" class="rounded" id="fax_number"name="supplier[fax]"value="{$supplier[fax]}" accept="numeric"  /><br />
    </div>
   </div>
         <div style="display:table-row;">
    <div style="display: table-cell; width:0px;">{$lang->email}</div>
    <div style="display: table-cell; padding:5px; ">
    <input  accept="email" class="rounded" type="text"  value="{$supplier[mainEmail]}" name="supplier[mainEmail]" />
    </div>
   </div>
   
       <div style="display:table-row;">
    <div style="display: table-cell; width:0px;">{$lang->website}</div>
    <div style="display: table-cell; padding:5px; ">
    <input class="rounded"type="text"  value="{$supplier[website]}" name="supplier[website]" />
    </div>
   </div>
 
      <div  class="thead">{$lang->chemicalproducts}</div>
        <table>
            <tbody  id="chemicalproduct_tbody" >
                <tr>
                    <td> 
                        <div style="display:table-row;">
                            <div style="display: table-cell; width:100px;">{$lang->chemical}</div>
                            <div style="display: table-cell; padding:5px; ">
                            <input class="rounded" type='text'    value="{$supplier[chemicalproducts][name]}"id='chemicalproducts_1_QSearch' autocomplete='off' size='40px'/>
              <input type='hidden' id='chemicalproducts_1_id' name='supplier[chemicalproducts][csid][]' value="{$supplier[chemicalproducts][csid]}"/>
                            <div id="searchQuickResults_chemicalproducts_1" class="searchQuickResults" style="display:none;"></div>
                            </div>
                        </div>
                    </td>
                    <td>
                    </td>
                </tr>
            </tbody>
        </table>
      
 <div style="display:table-row;">
    <div style="display: table-cell;width:100px;padding:5px;"> <img src="images/add.gif" id="addmore_chemicalproduct" alt="{$lang->add}"></div>
     </div>
		<div  class="thead">{$lang->representative}</div>

        <div style="display:table-row;">
        <div style="display: table-cell; width:100px;">{$lang->representative}</div>
          <div style="display: table-cell; padding:5px; ">
          
          	             <table>
            <tbody  id="representative_tbody">
                <tr>
                    <td> 
                        <div style="display:table-row;">
    
                            <div style="display: table-cell; padding:5px; ">
                    <input type='text' id='representative_1_QSearch' autocomplete='off' size='40px'/>
                    <input type='hidden' id='representative_1_id' name='supplier[representative][id][]'/>
                    <a href='#representative_1_id' id='addnew_sourcing/managesupplier_representative'><img src='images/addnew.png' border='0' alt='{$lang->add}'></a>
                    <div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div>
                    
                            </div>
                        </div>
                    </td>
                    <td>
                    </td>
                </tr>
            </tbody>
        </table>
          </div>
        </div>

 
               
 <div style="display:table-row;">
    <div style="display: table-cell;width:100px;padding:5px;"> <img src="images/add.gif" id="addmore_representative" alt="{$lang->add}"></div>
     </div>

      <div class="thead">{$lang->feedback}</div>
       <div style="display:table-row;">
    <div style="display: table-cell; width:450px;">{$lang->maturitylevel}</div>
     <div style="display: table-cell; width:100%;">{$relation_Maturity_level}</div>
    </div>
     <div style="display:table-row; ">
    
         <div style="display: table-cell;vertical-align:middle; width:300px;">{$lang->commentstoshare}</div>
        <div style="display: table-cell; padding:5px;vertical-align:top; width:100%;"> 
        <textarea tabindex="25" class="rounded texteditormin"   cols="35" rows="5"name="supplier[commentsToShare]" >{$supplier[commentsToShare]}</textarea>
  </div>
   </div>
    <div style="display:table-row;">
    <div style="display: table-cell; width:300px;;vertical-align:middle;">{$lang->marketingrecords}</div>
    <div style="display: table-cell; padding:5px;vertical-align:top; width:100%;">
    <textarea  tabindex="26" class="rounded texteditormin"  cols="35" rows="5"name="supplier[marketingRecords]" >{$supplier[marketingRecords]}</textarea>
    </div>
    </div>
    
        <div style="display:table-row;">
    <div style="display: table-cell; width:300px;vertical-align:middle;">{$lang->coBriefing}</div>
    <div style="display: table-cell; padding:5px;vertical-align:top; width:100%;">
    <textarea tabindex="27"  class="rounded texteditormin"   cols="35" rows="5"name="supplier[coBriefing]" >{$supplier[coBriefing]}</textarea>
    </div>
    </div>
    
        <div style="display:table-row;">
    <div style="display: table-cell; width:300px;vertical-align:middle;">{$lang->historical}</div>
    <div style="display: table-cell; padding:5px;vertical-align:top; width:100%;">
    <textarea tabindex="28"  class="rounded texteditormin"   cols="35" rows="5"name="supplier[historical]" >{$supplier[historical]}</textarea>
    </div>
    </div>
        
        <div style="display:table-row;">
    <div style="display: table-cell; width:300px;vertical-align:middle;">{$lang->sourcingRecords}</div>
    <div style="display: table-cell; padding:5px;vertical-align:middle; width:100%;">
    <textarea tabindex="29"  class="rounded texteditormin"   cols="35" rows="5"name="supplier[sourcingRecords]" >{$supplier[sourcingRecords]}</textarea>
    </div>
    </div>
        
        <div style="display:table-row;">
    <div style="display: table-cell; width:300px;vertical-align:middle;">{$lang->productFunction}</div>
    <div style="display: table-cell; padding:5px;vertical-align:middle; width:100%;">
    <textarea  tabindex="30" class="rounded texteditormin"   cols="35" rows="5"name="supplier[productFunction]" >{$supplier[productFunction]}</textarea>
    </div>
    </div>
     <div style="display:table-row;">
{$mark_blacklist}
      </div>
    
    <div  style="margin-bottom: 0.9em;"></div>
    
     <div style="display:table-row;">
    <div style="display: table-cell;width:10px;"> <input type="button" class="button" value="{$actiontype}" id="perform_sourcing/managesupplier_Button" /> </div>
      <div style="display: table-cell; width:10px;"> <input type="reset" class="button"value="{$lang->reset}"/>
 </div>
      </div>
   </form>
    <div style="display:table-row">
      <div style="display:table-cell;"id="perform_sourcing/managesupplier_Results"></div>
      </div> 


  </td>
</tr>
{$footer}

</body>
</html>