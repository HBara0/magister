  <div  id="{$communication_id}" class="{$class}">
   <span class="subtitle">{$communication_title}</span>
    <form   name="perform_{$communication_id}_{$contact_history[identifier]}_sourcing/supplierprofile_Form" action="#" method="post"  id="perform_{$communication_id}_{$contact_history[identifier]}_sourcing/supplierprofile_Form">
      <input type="hidden" value="{$action}" name="action" id="action" />
      <input name="contacthst[ssid]" type="hidden" value="{$newsupplierid}">
       <input name="contacthst[identifier]" type="hidden" value="{$identifier}">
      <div style="display:inline-block; width:100%; margin-left:15px;">
        <div style="display:inline-block; padding:5px;">
          <div style="display:inline-block; margin-left:5px;">{$lang->affilliate}		</div>
          <div style="display:inline-block; padding:5px;">{$affiliates_list}</div>
        </div>
        <div style="display:inline-block; padding:10px;">
          <div style="display:inline-block;">{$lang->chemicalname}</div>
          <div style="display:inline-block;">
     {$chemical_text_field}
{$chemical_hidden_field}
                  {$chemical_div_result}
      
          </div>
        </div>
        <div style="display:inline-block; padding:10px;">
          <div style="display:inline-block; ">{$lang->origin}</div>
          <div style="display:inline-block; padding:5px;">{$origins_list}</div>
       
          <div style="display:inline-block;">{$countries_list}</div>
        </div>
             </div>
               <fieldset>
  <legend class="subtitle">{$lang->optionalfields}</legend>
          <div style="display:inline-block; padding:10px;">
             <div style="display:inline-block;">{$lang->application}</div>
          <div style="display:inline-block; padding:10px">
            <input class="rounded"  name="contacthst[application]"  value="{$contact_history[application]}"type="text">
          </div>
        </div>
 
      <div style="display:inline-block; width:100%;margin-left:15px;">
        <div style="display:inline-block; padding:5px;">
          <div style="display:inline-block; margin-left:5px;">{$lang->date}</div>
          <div style="display:inline-block; padding:5px;">
            <input class="rounded"  name="contacthst[date]" type="text"  value="{$contact_history[date_output]}" id="{$datepicker_id['date']}" size="30" >
          </div>
        </div>
        <div style="display:inline-block; padding:5px;">
          <div style="display:inline-block; margin-left:5px;">{$lang->grade}</div>
          <div style="display:inline-block; padding:5px;">
      
       <input class="rounded"  name="contacthst[grade]" type="text"  value="{$contact_history[grade]}" size="30" >
          </div>
        </div>
        <div style="display:inline-block; padding:5px;">
          <div style="display:inline-block; margin-left:5px;">{$lang->market}</div>
          <div style="display:inline-block; padding:5px;"> {$product_segmentlist} </div>
        </div>
        <div style="display:inline-block; padding:5px;">
          <div style="display:inline-block; margin-left:5px;">{$lang->competitor}</div>
          <div style="display:inline-block; padding:5px;">
            <input class="rounded"  name="contacthst[competitors]" value="{$contact_history[competitors]}" type="text">
          </div>
        </div>
      </div>
      <div style="display:inline-block; padding:5px; vertical-align:text-top;margin-left:20px;">{$lang->description}</div>
      <div style="display:inline-block; margin-left:5px;">
        <textarea  class="rounded"  name="contacthst[description]" cols="45" rows="5">{$contact_history[description]}</textarea>
      </div>
      
  </fieldset>

      <!--Expanded Reportin START-->
      
      <div class='main'>
        <div class="content">{$lang->price}</div>
        <div class="content">{$lang->yes}
          <input  name="ispriceapproved" class="priceok" id="price_type"  type="radio" value="1">
          {$lang->no}
          <input class="pricenotOk"   name="ispriceapproved"  id="price_type" type="radio" value="0">
        </div>
      </div>
      <div class='main'>
        <div class='title'><span>
          <input id="paymentterms_check" name="" type="checkbox" value="paymentterms" disabled>
          </span>{$lang->paymenttermstitle}</div>
        <div id="paymentterms_body" style="display:none;">
          <div class='content'>
            <div class='content'>{$lang->paymentterms}</div>
            <div class='content'>
              <select name="contacthst[paymentTerms]">
                <option value="payment1">payment 1</option>
              </select>
            </div>
          </div>
          <div class='content'>
            <div class='content' style="vertical-align:middle;">{$lang->discussion}</div>
            <div class='content'>
              <textarea name="contacthst[Discussion]" cols="25" rows="5">{$contact_history[Discussion]}</textarea>
            </div>
          </div>
          <div class='question'>{$lang->yes}
            <input name="paymentapproved" class="stageapproved"  type="radio" value="1" disabled>
            {$lang->no}
            <input  name="paymentapproved" class="stagenotapproved"  type="radio" value="0">
          </div>
        </div>
      </div>
      <div class='main'>
        <div class='title'><span>
          <input id="customerdocument_check" name="" type="checkbox" value="customerdocument" disabled>
          </span>{$lang->customerdocument}</div>
        <div id="customerdocument_body" style="display:none;">
          <div class='content'>
            <div class='content'>{$lang->date}</div>
            <div class='content'>
              <input name="contacthst[customerDocumentDate]" id="{$datepicker_id['customerDocumentDate']}" type="text" value="{$contact_history[customerDocumentDate_output]}">
            </div>
          </div>
          <div class='content'>
            <div class='content'>{$lang->provideddoc}</div>
            <div class='content'>
              <textarea name="contacthst[customerDocument]" cols="25" rows="5">{$contact_history[customerDocument]}</textarea>
            </div>
          </div>
          <div class='question'>{$lang->specapproved}{$lang->yes}
            <input id="customerdocument_yes" name="customerdocument" class="stageapproved" type="radio" value="1">
            {$lang->no}
            <input  name="customerdocument"   class="stagenotapproved"id="customerdocument_yes" type="radio" value="0">
          </div>
        </div>
      </div>
      <div class='main'>
        <div class='title'><span>
          <input id="samplerequest_check" type="checkbox" value="samplerequest" disabled>
          </span>{$lang->samplerequest}</div>
        <div id="samplerequest_body" style="display:none;">
          <div class='content'>
            <div class='content'>{$lang->reqquantity}</div>
            <div class='content'>
              <input name="contacthst[requestedQuantity]" type="text" accept="numeric" value="{$contact_history[requestedQuantity]}" size="10">
              <select name="contacthst[requestedQuantityUom]">
                {$uom}
              </select>
            </div>
            <div class='content'>{$lang->reqdocuments}</div>
            <div class='content'>
              <input name="contacthst[requestedDocuments]" type="text"  size="25" value="{$contact_history[requestedDocuments]}">
            </div>
          </div>
          <div class='content'>
            <div class='content'>{$lang->recquantity}</div>
            <div class='content'>
              <input name="contacthst[receivedQuantity]" type="text" accept="numeric" value="{$contact_history[receivedQuantity]}">
              <select name="contacthst[receivedQantityUom]">
                {$uom}
              </select>
            </div>
            <div class='content'>{$lang->reqdocuments}</div>
            <div class='content'>
              <input name="contacthst[receivedDocuments]" type="text"  size="25" value="{$contact_history[receivedDocuments]}">
            </div>
          </div>
          <div class='question'>{$lang->sampleaccepted}{$lang->yes}
            <input   name="sampleaccepted" class="stageapproved" type="radio" value="1">
            {$lang->no}
            <input  name="sampleaccepted"  class="stagenotapproved"  type="radio" value="0">
            {$lang->na}
            <input  name="sampleaccepted" class="notapplocable"  type="radio" value="0">
          </div>
        </div>
      </div>
      <div class='main'>
        <div class='title'><span>
          <input id="customersample_check" name="" type="checkbox" value="customersample" disabled>
          </span>{$lang->customersample}</div>
        <div id="customersample_body" style="display:none;">
          <div class='content'>
            <div class='content'>{$lang->providedquantity}</div>
            <div class='content'>
              <input name="contacthst[providedQuantity]" type="text" accept="numeric" size="10" value="{$contact_history[providedQuantity]}">
              <select name="contacthst[providedQuantityUom]">
                {$uom}
              </select>
            </div>
            <div class='content'>{$lang->provideddocuments}</div>
            <div class='content'>
              <input name="contacthst[providedDocuments]" type="text"  size="25" value="{$contact_history[providedDocuments]}">
            </div>
            <div class='content'>{$lang->customerAnswer}</div>
            <div class='content'>
              <textarea name="contacthst[customerAnswer]" cols="20" rows="5">{$contact_history[customerAnswer]}</textarea>
            </div>
          </div>
          <div class='content'>
            <div class='content'  style="text-align:center;">{$lang->dateon}</div>
            <div class='content'>
              <input name="contacthst[receivedQuantityDate]" id="{$datepicker_id['receivedQuantityDate']}" type="text" value="{$contact_history[receivedQuantityDate_output]}">
            </div>
            <div class='content' style="text-align:center;">{$lang->dateon}</div>
            <div class='content'>
              <input name="contacthst[providedDocumentsDate]" id="{$datepicker_id['providedDocumentsDate']}" type="text" value="{$contact_history[providedDocumentsDate_output]}">
            </div>
            <div class='content' style="text-align:center;">{$lang->dateon}</div>
            <div class='content'>
              <input name="contacthst[customerAnswerDate]" id="{$datepicker_id['customerAnswerDate']}" type="text" value="{$contact_history[customerAnswerDate_output]}">
            </div>
          </div>
          <div class='question'>{$lang->compliantspec}{$lang->yes}
            <input name="compliantspec"  class="stageapproved"  type="radio" value="1">
            {$lang->no}
            <input name="compliantspec"    class="stagenotapproved"  type="radio" value="0">
            {$lang->na}
            <input  name="compliantspec" class="notapplocable"  type="radio" value="0">
          </div>
        </div>
      </div>
      <div class='main'>
        <div class='title'><span>
          <input id="industrial_check" name="" type="checkbox" value="industrial" disabled>
          {$lang->industrialtrial}</span></div>
        <div id="industrial_body" style="display:none;">
          <div class='content'>
            <div class='content'>{$lang->industrialquantity}</div>
            <div class='content'>
              <input name="contacthst[industrialQuantity]" type="text" accept="numeric"  value="{$contact_history[industrialQuantity]}" size="10">
              <select name="contacthst[industrialQuantityUom]">
                {$uom}
              </select>
            </div>
          </div>
          <div class='content'>
            <div class='content' style="text-align:center;">{$lang->provisiondate}</div>
            <div class='content'>
              <input name="contacthst[provisionDate]" id="{$datepicker_id['provisionDate']}" type="text" value="{$contact_history[provisionDate_output]}">
            </div>
          </div>
          <div class="content">
            <div class='content'>{$lang->trialresult}</div>
            <div class='content'>
              <textarea name="contacthst[trialResult]" cols="60" rows="4">{$contact_history[trialResult]}</textarea>
            </div>
          </div>
          <div class='question'>{$lang->productapproved}{$lang->yes}
            <input  name="productapproved"  class="stageapproved" type="radio" value="1">
            {$lang->no}
            <input   class="stagenotapproved"   name="productapproved" type="radio" value="0">
            {$lang->na}
            <input  name="productapproved" class="notapplocable"  type="radio" value="0">
          </div>
        </div>
      </div>
      <div class='main'>
        <div class='title'><span>
          <input id="commercialoffer_check" name="contacthst[commercialoffer]" type="checkbox" value="commercialoffer" disabled>
          </span>{$lang->offertitle}</div>
        <div id="commercialoffer_body" style="display:none;">
          <div class='content'>
            <div class='content'>{$lang->offer}</div>
            <div class='content'>
              <textarea name="contacthst[offerMade]" cols="50" rows="4">{$contact_history[offerMade]}</textarea>
            </div>
          </div>
          <div class='content'>
            <div class='content' style="text-align:center;">{$lang->offerdate}</div>
            <div class='content'>
              <input  value="{$contact_history[offerDate_output]}" name="contacthst[offerDate]" id="{$datepicker_id['offerDate']}" type="text">
            </div>
          </div>
          <div class="content">
            <div class='content'>{$lang->customerAnswer}</div>
            <div class='content'>
              <textarea name="contacthst[customerOfferAnswer]" cols="60" rows="4">{$contact_history[customerOfferAnswer]}</textarea>
            </div>
          </div>
          <div class="content">
            <div class='content' style="text-align:center;">{$lang->answerdate}</div>
            <div class='content'>
              <input name="contacthst[OfferAnswerDate]"  value="{$contact_history[OfferAnswerDate_output]}" id="{$datepicker_id['OfferAnswerDate']}" type="text">
            </div>
          </div>
          <div class='question'>{$lang->orderpassed}{$lang->yes}
            <input  name="contacthst[orderpassed]" class="stageapproved" " type="radio" value="1">
            {$lang->no}
            <input  name="contacthst[orderpassed]"  class="stagenotapproved"  type="radio" value="0">
          </div>
        </div>
      </div>
      <div class="main"  id="sourcingnotpossible_body" style="display:none;"  >
        <div class="title">{$lang->sourcingnotpossible}</div>
        <div>
          <div class="content">
            <div class='content'>{$lang->desciption}</div>
            <div class='content'>
              <textarea name="contacthst[sourcingnotPossibleDesc]" cols="70" rows="5"></textarea>
            </div>
          </div>
        </div>
      </div>
      
      <!--Expanded Reporting END-->
      
      <div style="display:inline-block;">
        <div style="display:inline-block;width:0px;">
          <input type="button" class="button" value="{$button_label}" id="perform_{$communication_id}_{$contact_history[identifier]}_sourcing/supplierprofile_Button" />
        </div>
      </div>
    </form>
    <div style="display:inline-block; margin-top:5px;">
      <div style="display:inline-block;" id="perform_{$communication_id}_{$contact_history[identifier]}_sourcing/supplierprofile_Results"></div>
    </div>

  </div>

