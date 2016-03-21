<div id="{$communication_id}" class="{$class}" style="width:100%; margin-top: 20px;">
    <form   name="perform_{$communication_id}_{$contact_history[identifier]}_sourcing/supplierprofile_Form" action="#" method="post"  id="perform_{$communication_id}_{$contact_history[identifier]}_sourcing/supplierprofile_Form">
        <input type="hidden" value="{$action}" name="action" id="action" />
        <input name="contacthst[ssid]" type="hidden" value="{$newsupplierid}">
        <input name="contacthst[identifier]" type="hidden" value="{$identifier}">
        <span class="subtitle">{$communication_title}</span>
        <div style="display:inline-block; width:100%; margin-left:15px;">
            <div style="display:inline-block; padding:5px;">
                <div style="display:inline-block; margin-left:5px;">{$lang->affilliate}</div>
                <div style="display:inline-block; padding:5px;">   <select name="contacthst[affid]">
                        {$affiliates_list}
                    </select></div>
            </div>
            <div style="display:inline-block; padding:10px;">
                <div style="display:inline-block;">{$lang->chemicalname}</div>
                <div style="display:inline-block;">
                    {$chemical_text_field}
                    {$chemical_hidden_field}
                    {$chemical_div_result}
                </div>
            </div>
            <div style="display:inline-block; padding:5px;">
                <div style="display:inline-block; margin-left:5px;">{$lang->date}</div>
                <div style="display:inline-block; padding:5px;">
                    <input class="rounded"  name="contacthst[date]" type="text"  value="{$contact_history[date_output]}" id="{$datepicker_id['date']}" size="30" >
                </div >
            </div>
        </div>
        <fieldset class="altrow2">
            <legend class="subtitle">{$lang->optionalfields}</legend>
            <div style="display:inline-block; width: 45%;">
                <div style="padding:5px;">
                    <div style="display:inline-block; margin-left:5px; width:20%;">{$lang->origin}</div>
                    <div style="display:inline-block; padding:5px;">{$origins_list}</div>
                </div>
                <div style="padding:5px;">
                    <div style="display:inline-block; margin-left:5px; width:20%;">{$lang->grade}</div>
                    <div style="display:inline-block; padding:5px;">
                        <input class="rounded"  name="contacthst[grade]" type="text"  value="{$contact_history[grade]}" size="30" >
                    </div>
                </div>
                <div style="padding:5px;">
                    <div style="display:inline-block; margin-left:5px; width:20%;">{$lang->market}</div>
                    <div style="display:inline-block; padding:5px;"> {$product_segmentlist} </div>
                </div>
                <div style="padding:5px;">
                    <div style="display:inline-block; margin-left:5px; width:20%;">{$lang->application}</div>
                    <div style="display:inline-block; padding:5px;">
                        <input class="rounded"  name="contacthst[application]"  value="{$contact_history[application]}"type="text">
                    </div>
                </div>
                <div style="padding:5px;">
                    <div style="display:inline-block; margin-left:5px; width:20%;">{$lang->competitor}</div>
                    <div style="display:inline-block; padding:5px;">
                        <input class="rounded"  name="contacthst[competitors]" value="{$contact_history[competitors]}" type="text">
                    </div>
                </div>
            </div>

            <div style="display:inline-block; width: 45%; vertical-align:top;">
                <div style="display:inline-block; margin-left:5px;">
                    <div style="display:inline-block; margin-left:5px;">{$lang->description}</div>
                    <textarea  class="rounded"  name="contacthst[description]" cols="45" rows="5">{$contact_history[description]}</textarea>
                </div>
            </div>
        </fieldset>
        <!--Expanded Reporting START-->
        <div class="subtitle" style="margin-top: 10px;">{$lang->productdevelopment}</div>
        <div class='main'>
            <div class="content">{$lang->price}</div>
            <div class="content">
                <input  name="contacthst[isPriceApproved]" class="priceok" id="price_type" type="radio" value="1"{$radiobuttons_check[isPriceApproved][1]}>
                {$lang->yes}
                <input class="pricenotOk" name="contacthst[isPriceApproved]" id="price_type" type="radio" value="0"{$radiobuttons_check[isPriceApproved][0]}>
                {$lang->no}
                <input class="priceok" name="contacthst[isPriceApproved]" id="price_type" type="radio" value="3"{$radiobuttons_check[isPriceApproved][3]}>
                {$lang->nobutcontinue}
            </div>
        </div>
        <div class='main'>
            <div class='title'><span>
                    <input id="paymentterms_check_{$identifier}" name="" type="checkbox" value="paymentterms"{$disabled_checkboxes[paymentterms]}{$checked_checkboxes[paymentterms]}>
                </span>{$lang->paymenttermstitle}</div>
            <div id="paymentterms_body_{$identifier}" style="display:none;" class="{$ch_productsection}">
                <div class='content'>
                    <div class='content'>{$lang->paymentterms}</div>
                    <div class='content'>
                        <select name="contacthst[paymentTerms]">
                            <option value="0"{$selected_selectitems[payementTerms][0]}>0 {$lang->days}</option>
                            <option value="30"{$selected_selectitems[payementTerms][30]}>30 {$lang->days}</option>
                            <option value="45"{$selected_selectitems[payementTerms][45]}>45 {$lang->days}</option>
                            <option value="60"{$selected_selectitems[payementTerms][60]}>60 {$lang->days}</option>
                            <option value="90"{$selected_selectitems[payementTerms][90]}>90 {$lang->days}</option>
                            <option value="120"{$selected_selectitems[payementTerms][120]}>120 {$lang->days}</option>
                            <option value="180"{$selected_selectitems[payementTerms][180]}>180 {$lang->days}</option>
                        </select>
                    </div>
                </div>
                <div class='content'>
                    <div class='content' style="vertical-align:middle;">{$lang->discussion}</div>
                    <div class='content'>
                        <textarea name="contacthst[Discussion]" cols="25" rows="5">{$contact_history[discussion]}</textarea>
                    </div>
                </div>
                <div class='question'>{$lang->approvedterms}
                    <input name="contacthst[isPaymentApproved]" class="stageapproved"  type="radio" value="1"{$radiobuttons_check[isPaymentApproved][1]}>
                    {$lang->yes}
                    <input  name="contacthst[isPaymentApproved]" class="stagenotapproved"  type="radio" value="0"{$radiobuttons_check[isPaymentApproved][0]}>
                    {$lang->no}
                    <input name="contacthst[isPaymentApproved]" class="stageapproved" type="radio" value="3"{$radiobuttons_check[isPaymentApproved][3]}>
                    {$lang->nobutcontinue}
                </div>
            </div>
        </div>
        <div class='main'>
            <div class='title'><span>
                    <input id="customerdocument_check" name="" type="checkbox" value="customerdocument"{$disabled_checkboxes[customerdocument]}{$checked_checkboxes[customerdocument]}>
                </span>{$lang->customerdocument}</div>
            <div id="customerdocument_body_{$identifier}" style="display:none;">
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
                <div class='question'>{$lang->specapproved}
                    <input id="customerdocument_yes" name="contacthst[isCustomerdocumentApproved]" class="stageapproved" type="radio" value="1"{$radiobuttons_check[isCustomerdocumentApproved][1]}>
                    {$lang->yes}
                    <input name="contacthst[isCustomerdocumentApproved]"  class="stagenotapproved"id="customerdocument_yes" type="radio" value="0"{$radiobuttons_check[isCustomerdocumentApproved][0]}>
                    {$lang->no}
                    <input name="contacthst[isCustomerdocumentApproved]" class="stageapproved" type="radio" value="3"{$radiobuttons_check[isCustomerdocumentApproved][3]}>
                    {$lang->nobutcontinue}
                </div>
            </div>
        </div>
        <div class='main'>
            <div class='title'><span>
                    <input id="samplerequest_check" type="checkbox" value="samplerequest"{$disabled_checkboxes[samplerequest]}{$checked_checkboxes[samplerequest]}>
                </span>{$lang->samplerequest}</div>
            <div id="samplerequest_body_{$identifier}" style="display:none;">
                <div class='content'>
                    <div class='content'>{$lang->reqquantity}</div>
                    <div class='content'>
                        <input name="contacthst[requestedQuantity]" type="text" accept="numeric" size="10">
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
                        <input name="contacthst[receivedQuantity]" type="text" accept="numeric">
                        <select name="contacthst[receivedQantityUom]">
                            {$uom}
                        </select>
                    </div>
                    <div class='content'>{$lang->recdocuments}</div>
                    <div class='content'>
                        <input name="contacthst[receivedDocuments]" type="text"  size="25" value="{$contact_history[receivedDocuments]}">
                    </div>
                </div>
                <div class='question'>{$lang->sampleaccepted}
                    <input   name="contacthst[isSampleAccepted]"class="stageapproved" type="radio" value="1"{$radiobuttons_check[isSampleAccepted][1]}>
                    {$lang->yes}
                    <input  name="contacthst[isSampleAccepted]" class="stagenotapproved"  type="radio" value="0"{$radiobuttons_check[isSampleAccepted][0]}>
                    {$lang->no}
                    <input   name="contacthst[isSampleAccepted]" class="notapplocable"  type="radio" value="2"{$radiobuttons_check[isSampleAccepted][2]}>
                    {$lang->na}
                    <input name="contacthst[isSampleAccepted]" class="stageapproved" type="radio" value="3"{$radiobuttons_check[isSampleAccepted][3]}>
                    {$lang->nobutcontinue}
                </div>
            </div>
        </div>
        <div class='main'>
            <div class='title'><span>
                    <input id="customersample_check" name="" type="checkbox" value="customersample"{$disabled_checkboxes[customersample]}{$checked_checkboxes[customersample]}>
                </span>{$lang->customersample}</div>
            <div id="customersample_body_{$identifier}" style="display:none;">
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
                    <div class='content'>{$lang->customeranswer}</div>
                    <div class='content'>
                        <textarea name="contacthst[customerAnswer]" cols="20" rows="5">{$contact_history[customerAnswer]}</textarea>
                    </div>
                </div>
                <div class='content'>
                    <div class='content'  style="text-align:center;">{$lang->dateon}</div>
                    <div class='content'>
                        <input name="contacthst[receivedQuantityDate]" id="{$datepicker_id[receivedQuantityDate]}" type="text" value="{$contact_history[receivedQuantityDate_output]}">
                    </div>
                    <div class='content' style="text-align:center;">{$lang->dateon}</div>
                    <div class='content'>
                        <input name="contacthst[providedDocumentsDate]" id="{$datepicker_id[providedDocumentsDate]}" type="text" value="{$contact_history[providedDocumentsDate_output]}">
                    </div>
                    <div class='content' style="text-align:center;">{$lang->dateon}</div>
                    <div class='content'>
                        <input name="contacthst[customerAnswerDate]" id="{$datepicker_id[customerAnswerDate]}" type="text" value="{$contact_history[customerAnswerDate_output]}">
                    </div>
                </div>
                <div class='question'>{$lang->compliantspec}
                    <input name="contacthst[isCompliantSpec]"  class="stageapproved"  type="radio" value="1"{$radiobuttons_check[isCompliantSpec][1]}>
                    {$lang->yes}
                    <input  name="contacthst[isCompliantSpec]"  class="stagenotapproved"  type="radio" value="0"{$radiobuttons_check[isCompliantSpec][0]}>
                    {$lang->no}
                    <input   name="contacthst[isCompliantSpec]"  class="notapplocable"  type="radio" value="0"{$radiobuttons_check[isCompliantSpec][2]}>
                    {$lang->na}
                    <input name="contacthst[isCompliantSpec]" class="stageapproved" type="radio" value="3"{$radiobuttons_check[isCompliantSpec][3]}>
                    {$lang->nobutcontinue}
                </div>
            </div>
        </div>
        <div class='main'>
            <div class='title'><span>
                    <input id="industrial_check" name="" type="checkbox" value="industrial"{$disabled_checkboxes[industrial]}{$checked_checkboxes[industrial]}>
                    {$lang->industrialtrial}</span></div>
            <div id="industrial_body_{$identifier}" style="display:none;">
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
                <div class='question'>{$lang->productapproved}
                    <input  name="contacthst[isProductApproved]" class="stageapproved" type="radio" value="1"{$radiobuttons_check[isProductApproved][1]}>
                    {$lang->yes}
                    <input   class="stagenotapproved" name="contacthst[isProductApproved]" type="radio" value="0"{$radiobuttons_check[isProductApproved][0]}>
                    {$lang->no}
                    <input   name="contacthst[isProductApproved]" class="notapplocable" type="radio" value="2"{$radiobuttons_check[isProductApproved][2]}>
                    {$lang->na}
                    <input name="contacthst[isProductApproved]" class="stageapproved" type="radio" value="3"{$radiobuttons_check[isProductApproved][3]}>
                    {$lang->nobutcontinue}
                </div>
            </div>
        </div>
        <div class='main'>
            <div class='title'>
                <input id="commercialoffer_check" name="contacthst[commercialoffer]" type="checkbox" value="commercialoffer"{$disabled_checkboxes[commercialoffer]}{$checked_checkboxes[commercialoffer]}>
                {$lang->offertitle}</div>
            <div id="commercialoffer_body_{$identifier}" style="display:none;">
                <div class='content' style="width:50%;">
                    <div class='content' style="width:40%;">{$lang->offermade}</div>
                    <div class='content'>
                        <textarea name="contacthst[offerMade]" cols="50" rows="4">{$contact_history[offerMade]}</textarea>
                    </div>
                </div>
                <div class='content' style="width:40%; float: right;">
                    <div class='content' style="text-align:center;">{$lang->offerdate}</div>
                    <div class='content'>
                        <input  value="{$contact_history[offerDate_output]}" name="contacthst[offerDate]" id="{$datepicker_id[offerDate]}" type="text">
                    </div>
                </div>
                <div class="content" style="width:50%;">
                    <div class='content' style="width:40%;">{$lang->customeranswer}</div>
                    <div class='content'>
                        <textarea name="contacthst[customerOfferAnswer]" cols="60" rows="4">{$contact_history[customerOfferAnswer]}</textarea>
                    </div>
                </div>
                <div class="content" style="width:40%; float: right;">
                    <div class='content' style="text-align:center;">{$lang->answerdate}</div>
                    <div class='content'>
                        <input name="contacthst[OfferAnswerDate]"  value="{$contact_history[OfferAnswerDate_output]}" id="{$datepicker_id['OfferAnswerDate']}" type="text">
                    </div>
                </div>
                <div class='question'>{$lang->orderpassed}
                    <input name="contacthst[isOrderPassed]" class="stageapproved" type="radio" value="1"{$radiobuttons_check[isOrderPassed][1]}>
                    {$lang->yes}
                    <input name="contacthst[isOrderPassed]" class="stagenotapproved"  type="radio" value="0"{$radiobuttons_check[isOrderPassed][0]}>
                    {$lang->no} </div>
            </div>
        </div>
        <div class="main unapproved" id="sourcingnotpossible_body_{$identifier}" style="display:none;">
            <div class="title" style="font-weight:bold;">{$lang->sourcingnotpossible}</div>
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
            <div style="display: inline-block; width:0px;">
                <input type="button" class="button" value="{$button_label}" id="perform_{$communication_id}_{$contact_history[identifier]}_sourcing/supplierprofile_Button" />
            </div>
        </div>
    </form>
    <div style="display:inline-block; margin-top:5px;" id="results_{$communication_id}_{$contact_history[identifier]}_sourcing/supplierprofile_Results">
        <div style="display:inline-block;" id="perform_{$communication_id}_{$contact_history[identifier]}_sourcing/supplierprofile_Results"></div>
    </div>
</div>