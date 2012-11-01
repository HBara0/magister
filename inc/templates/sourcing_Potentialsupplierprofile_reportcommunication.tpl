<div  id="reportcommunication"  style="display:inline-block;padding: 25px;">

  <form   name="perform_sourcing/supplierprofile_Form" action="{$_SERVER[QUERY_STRING]}" method="post"  id="perform_sourcing/supplierprofile_Form">
    <input class="rounded" type="hidden" value="do_savecommunication" name="action" id="action" />
    <input name="contacthst[ssid]" type="hidden" value="{$newsupplierid}">
 		<div class="subtitle">{$lang->reportcommunication}</div>
        <div style="display:inline-block; margin-right:33px;">
        
           <div style="display:table-row; padding:5px;">
        <div style="display:table-cell; margin-left:5px;">{$lang->affilliate}</div>
        <div style="display:table-cell; padding:5px;">{$affiliates_list}</div>
        
    </div>
               <div style="display:table-row; padding:5px;">
        <div style="display:table-cell; margin-left:5px;">{$lang->chemicalname}</div>
        <div style="display:table-cell;padding:5px;">
   <input class="rounded" type='text' value="{$contacthst[chemical]}"id='chemicalproducts_1_QSearch' autocomplete='off' size='40px'/>
              <input type='hidden' id='chemicalproducts_1_id' name='contacthst[chemical]' value="{$contacthst[chemical]}"/>
                            <div id="searchQuickResults_chemicalproducts_1" class="searchQuickResults" style="display:none;"></div>
 
   <div id="searchQuickResults_chemical_1" class="searchQuickResults" style="display:none;"></div>
        </div>
        
    </div>
    
<div style="display:table-row; padding:5px;">
    <div style="display:table-cell; margin-left:5px;">{$lang->origin}</div>
    <div style="display:table-cell; padding:5px;">{$countries_list}</div>
 
</div>
    
<div style="display:table-row; padding:5px;">
    <div style="display:table-cell; margin-left:5px;">{$lang->application}</div>
    <div style="display:table-cell; padding:5px;"><input class="rounded"  name="contacthst[application]" type="text"></div>
 
</div>
   
    </div>




<div style="display:inline-block; padding:5px;">
           <div style="display:table-row; padding:5px;">
        <div style="display:table-cell; margin-left:5px;">{$lang->date}</div>
        <div style="display:table-cell; padding:5px;"><input class="rounded"  name="contacthst[date]" type="text" id="pickDate" size="30" value=""></div>
       
        </div>
            
        <div style="display:table-row; padding:5px;">
        <div style="display:table-cell; margin-left:5px;">{$lang->grade}</div>
        <div style="display:table-cell; padding:5px;"><select name="contacthst[grade]">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        </select></div>
     </div>
            
    <div style="display:table-row; padding:5px;">
        <div style="display:table-cell; margin-left:5px;">{$lang->market}</div>
        <div style="display:table-cell; padding:5px;">
		{$product_segmentlist}
   
   		</div>
     </div>
     <div style="display:table-row; padding:5px;">
        <div style="display:table-cell; margin-left:5px;">{$lang->competitor}</div>
        <div style="display:table-cell; padding:5px;"><input class="rounded"  name="contacthst[competitors]" type="text"></div>
     </div>
     
    </div>
    <div style="display:table-row; padding:5px;">
     <div style="display:table-cell; margin-left:5px;">
    <textarea  class="rounded"  name="contacthst[description]" cols="45" rows="5"></textarea></div>
    </div>
     <div style="display:table-row;">
    <div style="display: table-cell;width:0px;"> <input type="button" class="button" value="{$lang->save}" id="perform_sourcing/supplierprofile_Button" /> </div>
      
      </div>
</form>

    <div style="display:table-row">
      <div style="display:table-cell;"id="perform_sourcing/supplierprofile_Results"></div>
      </div> 

</div>


