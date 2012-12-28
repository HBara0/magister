<div  id="reportcommunication">

  <form   name="perform_sourcing/supplierprofile_Form" action="{$_SERVER[QUERY_STRING]}" method="post"  id="perform_sourcing/supplierprofile_Form">
    <input class="rounded" type="hidden" value="do_savecommunication" name="action" id="action" />
       <input name="contacthst[identifier]" type="hidden" value="{$identifier}">
    <input name="contacthst[ssid]" type="hidden" value="{$newsupplierid}">
 		<span class="subtitle">{$lang->reportcommunication}</span>
        <div style="display:inline-block; width:100%; margin-left:15px;">
        
           <div style="display:inline-block; padding:5px;">
        <div style="display:inline-block; margin-left:5px;">{$lang->affilliate}</div>
        <div style="display:inline-block; padding:5px;">{$affiliates_list}</div>
        
    </div>
     <div style="display:inline-block; padding:10px;">
        <div style="display:inline-block;">{$lang->chemicalname}</div>
        <div style="display:inline-block;">
   <input class="rounded" type='text' value="{$contacthst[chemical]}"id='chemicalproducts_1_QSearch' autocomplete='off' size='40px'/>
              <input type='hidden' id='chemicalproducts_1_id' name='contacthst[chemical]' value="{$contacthst[chemical]}"/>
                            <div id="searchQuickResults_chemicalproducts_1" class="searchQuickResults" style="display:none;"></div>
 
   <div id="searchQuickResults_chemical_1" class="searchQuickResults" style="display:none;"></div>
        </div>
        
    </div>
    
<div style="display:inline-block; padding:5px;">
    <div style="display:inline-block; margin-left:5px;">{$lang->origin}</div>
    <div style="display:inline-block; padding:5px;">{$countries_list}</div>
 
</div>
     </div>
  <fieldset>
  <legend class="subtitle">{$lang->optionalfields}</legend>
  <div style="display:inline-block; padding:5px;">
    <div style="display:inline-block; margin-left:5px;">{$lang->application}</div>
    <div style="display:inline-block; padding:5px;"><input class="rounded"  name="contacthst[application]" type="text"></div>
 
</div>
   
   




<div style="display:inline-block; width:100%;margin-left:15px;">
           <div style="display:inline-block; padding:5px;">
        <div style="display:inline-block; margin-left:5px;">{$lang->date}</div>
        <div style="display:inline-block; padding:5px;"><input class="rounded"  name="contacthst[date]" type="text" id="pickDate_7" size="30" value=""></div>
       
        </div>
            
        <div style="display:inline-block; padding:5px;">
        <div style="display:inline-block; margin-left:5px;">{$lang->grade}</div>
        <div style="display:inline-block; padding:5px;"><input type="text" name="contacthst[grade]">
  </div>
     </div>
            
    <div style="display:inline-block; padding:5px;">
        <div style="display:inline-block; margin-left:5px;">{$lang->market}</div>
        <div style="display:inline-block; padding:5px;">
		{$product_segmentlist}
   
   		</div>
     </div>
     <div style="display:inline-block; padding:5px;">
        <div style="display:inline-block; margin-left:5px;">{$lang->competitor}</div>
        <div style="display:inline-block; padding:5px;"><input class="rounded"  name="contacthst[competitors]" type="text"></div>
     </div>
     
    </div>
    <div style="display:inline-block; padding:5px; margin-left:20px;">
     <div style="display:inline-block; margin-left:5px;">
             <div style="display:inline-block; margin-left:5px;">{$lang->description}</div>

    <textarea  class="rounded"  name="contacthst[description]" cols="45" rows="5"></textarea></div>
    </div>
  
  </fieldset>  
    

    
    <!--Expanded Reporting START-->
 
 <div class='main'>
 	
 	<div class="content">{$lang->price}</div>
    	<div class="content"><input  name="ispriceapproved" class="priceok" id="price_type"  type="radio" value="1">{$lang->yes}
    <input class="pricenotOk"   name="ispriceapproved"  id="price_type" type="radio" value="0">{$lang->no} </div>


 </div>

  <div class='main'>
  <div class='title'><span><input id="paymentterms_check" name="" type="checkbox" value="paymentterms" disabled></span>{$lang->paymenttermstitle}</div>
    <div id="paymentterms_body" style="display:none;"> 
    <div class='content'> 
      <div class='content'>{$lang->paymentterms}</div>
      <div class='content'><select name="contacthst[paymentTerms]"><option value="payment1">payment 1</option></select></div>
      </div>
      
      <div class='content'> 
      <div class='content' style="vertical-align:middle;">{$lang->discussion}</div>
      <div class='content'><textarea name="contacthst[Discussion]" cols="25" rows="5"></textarea></div>
      </div>
             <div class='question'>{$lang->approvedterms}<input name="paymentapproved" class="stageapproved"  type="radio" value="1" disabled>{$lang->yes}
<input  name="paymentapproved" class="stagenotapproved"  type="radio" value="0"> {$lang->no}</div>
     </div> 
</div>
 

 <div class='main'>
  <div class='title'><span><input id="customerdocument_check" name="" type="checkbox" value="customerdocument" disabled></span>{$lang->customerdocument}</div>
    <div id="customerdocument_body" style="display:none;"> 
    <div class='content'> 
      <div class='content'>{$lang->date}</div>
      <div class='content'><input name="contacthst[customerDocumentDate]" id="pickDate_1" type="text"></div>
      </div>
      
      <div class='content'> 
      <div class='content'>{$lang->provideddoc}</div>
      <div class='content'><textarea name="contacthst[customerDocument]" cols="25" rows="5"></textarea></div>
      </div>
             <div class='question'>{$lang->specapproved}
             <input id="customerdocument_yes" name="customerdocument" class="stageapproved" type="radio" value="1">{$lang->yes}<input  name="customerdocument"   class="stagenotapproved"id="customerdocument_yes" type="radio" value="0">{$lang->no}</div>
     </div> 

  
 </div>



 <div class='main'>
  <div class='title'><span><input id="samplerequest_check" type="checkbox" value="samplerequest" disabled></span>{$lang->samplerequest}</div>
    <div id="samplerequest_body" style="display:none;"> 
    <div class='content'> 
      <div class='content'>{$lang->reqquantity}</div>
      <div class='content'><input name="contacthst[requestedQuantity]" type="text" accept="numeric" size="10"><select name="contacthst[requestedQuantityUom]">{$uom}</select></div>
     
        <div class='content'>{$lang->reqdocuments}</div>
      <div class='content'><input name="contacthst[requestedDocuments]" type="text"  size="25"></div>
      
    </div>
      <div class='content'> 
      <div class='content'>{$lang->recquantity}</div>
      <div class='content'><input name="contacthst[receivedQuantity]" type="text" accept="numeric"><select name="contacthst[receivedQantityUom]">{$uom}</select></div>
        <div class='content'>{$lang->recdocuments}</div>
      <div class='content'><input name="contacthst[receivedDocuments]" type="text"  size="25"></div>
    </div>
    
    
    <div class='question'>{$lang->sampleaccepted}<input   name="sampleaccepted" class="stageapproved" type="radio" value="1">{$lang->yes}<input  name="sampleaccepted"  class="stagenotapproved"  type="radio" value="0">{$lang->no}<input  name="sampleaccepted" class="notapplocable"  type="radio" value="0">{$lang->na}</div>
  </div>
</div>
 
   
   
   
    <div class='main'>
  <div class='title'><span><input id="customersample_check" name="" type="checkbox" value="customersample" disabled></span>{$lang->customersample}</div>
    <div id="customersample_body" style="display:none;"> 
    <div class='content'> 
      <div class='content'>{$lang->providedquantity}</div>
      <div class='content'><input name="contacthst[providedQuantity]" type="text" accept="numeric" size="10"><select name="contacthst[providedQuantityUom]">{$uom}</select>
     </div>
      
        <div class='content'>{$lang->provideddocuments}</div>
      <div class='content'><input name="contacthst[providedDocuments]" type="text"  size="25"></div>
      <div class='content'>{$lang->customerAnswer}</div>
      <div class='content'><textarea name="contacthst[customerAnswer]" cols="20" rows="5"></textarea></div>
      
    </div>
   
      <div class='content'> 
      <div class='content'  style="text-align:center;">{$lang->dateon}</div>
      <div class='content'><input name="contacthst[receivedQuantityDate]" id="pickDate_2" type="text"></div>
       <div class='content' style="text-align:center;">{$lang->dateon}</div>
      <div class='content'><input name="contacthst[providedDocumentsDate]" id="pickDate_3" type="text"></div>
             <div class='content' style="text-align:center;">{$lang->dateon}</div>
      <div class='content'><input name="contacthst[customerAnswerDate]" id="pickDate_8" type="text"></div>
    </div>
   
    
    <div class='question'>{$lang->compliantspec}<input name="compliantspec"  class="stageapproved"  type="radio" value="1">{$lang->yes}<input name="compliantspec"    class="stagenotapproved"  type="radio" value="0">{$lang->no}<input  name="compliantspec" class="notapplocable"  type="radio" value="0">{$lang->na}</div>
  </div>
</div>
 
    
    <div class='main'>
  <div class='title'><span><input id="industrial_check" name="" type="checkbox" value="industrial" disabled>{$lang->industrialtrial}</span></div>
    <div id="industrial_body" style="display:none;"> 
    <div class='content'> 
      <div class='content'>{$lang->industrialquantity}</div>
      <div class='content'><input name="contacthst[industrialQuantity]" type="text" accept="numeric" size="10"><select name="contacthst[industrialQuantityUom]">{$uom}</select></div> 
 
    </div>
      <div class='content'> 
      <div class='content' style="text-align:center;">{$lang->provisiondate}</div>
      <div class='content'><input name="contacthst[provisionDate]" id="pickDate_4" type="text"></div>
    </div>
    <div class="content">
        <div class='content'>{$lang->trialresult}</div>
        <div class='content'><textarea name="contacthst[trialResult]" cols="60" rows="4"></textarea></div>
    </div>
   
    
    <div class='question'>{$lang->productapproved}<input  name="productapproved"  class="stageapproved" type="radio" value="1">{$lang->yes}<input   class="stagenotapproved"   name="productapproved" type="radio" value="0">{$lang->no}<input  name="productapproved" class="notapplocable"  type="radio" value="0">{$lang->na}</div>
  </div>
</div>
   
   
   
       
    <div class='main'>
  <div class='title'><span><input id="commercialoffer_check" name="contacthst[commercialoffer]" type="checkbox" value="commercialoffer" disabled></span>{$lang->offertitle}</div>
    <div id="commercialoffer_body" style="display:none;"> 
    <div class='content'> 
      <div class='content'>{$lang->offer}</div>
      <div class='content'><textarea name="contacthst[offerMade]" cols="50" rows="4"></textarea></div> 
    </div>
      <div class='content'> 
      <div class='content' style="text-align:center;">{$lang->offerdate}</div>
      <div class='content'><input name="contacthst[offerDate]" id="pickDate_5" type="text"></div>
    </div>
    <div class="content">
        <div class='content'>{$lang->customerAnswer}</div>
        <div class='content'><textarea name="contacthst[customerOfferAnswer]" cols="60" rows="4"></textarea></div>
            </div>
             <div class="content">
           <div class='content' style="text-align:center;">{$lang->answerdate}</div>
              <div class='content'>
              <input name="contacthst[OfferAnswerDate]" id="pickDate_6" type="text"></div>
    </div>
   
    
    <div class='question'>{$lang->orderpassed}<input  name="contacthst[orderpassed]" class="stageapproved" type="radio" value="1">{$lang->yes}<input  name="contacthst[orderpassed]"  class="stagenotapproved"  type="radio" value="0">{$lang->no}
   
    </div>
  </div>
</div>



<div class="main"  id="sourcingnotpossible_body" style="display:none;"  >
<div class="title">{$lang->sourcingnotpossible}</div>
<div> 

<div class="content">
  <div class='content'>{$lang->desciption}</div>
    <div class='content'><textarea name="contacthst[sourcingnotPossibleDesc]" cols="70" rows="5"></textarea></div>
</div>
</div>
</div>


    <!--Expanded Reporting END-->
    
    
    
    
     <div style="display:inline-block;">
    <div style="display: inline-block;width:0px;"> <input type="button" class="button" value="{$lang->save}" id="perform_sourcing/supplierprofile_Button" /> </div>
 </div>


 
</form>

    <div style="display:inline-block; margin-top:5px;">
      <div style="display:inline-block;"id="perform_sourcing/supplierprofile_Results"></div>
      </div> 

</div>

