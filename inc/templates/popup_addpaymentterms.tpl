<div id="popup_addpaymentterms" title="{$lang->managepaymentterms}">
    <form action="#" method="post" id="perform_entities/paymenttermslist_Form" name="perform_entities/paymenttermslist_Form">
        <input type="hidden" name="action" value="do_perform_addpaymentterms" />
        <div style="margin:5px;">
            <div style="display:inline-block;width:30%;">{$lang->title}</div>
            <div style="display:inline-block;">
                <input type="text" name="paymentterms[title]" id="paymentterms_title" value="$paymentterms->title" required="required"/>
                <input type="hidden" value="{$paymentterms->ptid}" name="paymentterms[ptid]" /></div>
        </div>
        <div style="margin:5px;">
            <div style="display:inline-block;width:30%;">{$lang->overduepaymentdays}</div>
            <div style="display:inline-block;"><input type="number" id="paymentterms_overduePaymentDays" name="paymentterms[overduePaymentDays]" value="{$paymentterms->overduePaymentDays}" required="required" step="1" min="0"/></div>
        </div>
        <div style="margin:5px;">
            <div style="display:inline-block;width:30%;vertical-align: top;">{$lang->description}</div>
            <div style="display:inline-block;"> <textarea cols="30" rows="5" id="paymentterm_description" name="paymentterms[description]" tabindex="5">{$paymentterms->description}</textarea></div>
        </div>
        <div style="margin:5px;">
            <input name="paymentterms[nextBusinessDay]" id="paymentterms_nextBusinessDay" type="checkbox" value="1" {$checked[nextBusinessDay]}> {$lang->nextbusinessday}</td>
        </div>

        <div>
            <input type='button' id='perform_entities/paymenttermslist_Button' value='{$lang->savecaps}' class='button'/>  <input type="reset" value="{$lang->reset}" class="button"/>
            <div style="display:table-cell;"><div id="perform_entities/paymenttermslist_Results"></div>
            </div>
        </div>


    </form>
</div>