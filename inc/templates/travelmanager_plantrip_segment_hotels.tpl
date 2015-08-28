<div style="display:block;width:100%;  border:1px solid #FCEFA1;  padding:5px;">
    <div><input aria-describedby="ui-tooltip-155" data-reqparent="children-numnight_segacc_{$approved_hotels[tmhid]}_{$sequence}-pricenight_segacc_{$approved_hotels[tmhid]}_{$sequence}" name="segment[{$sequence}][tmhid][{$checksum}][tmhid]" id="segment[{$sequence}][{$checksum}][tmhid]" value="{$approved_hotels[tmhid]}" type="checkbox" {$hotelchecked}>{$hotel->name} <span>{$review_tools}</span><br />{$hotel->addressLine1}</div>
    <div>
        City: {$cityname}
    </div><div style="display:inline-block;padding:2px;width:20%; font-weight: bold;" id="avg_segacc_{$approved_hotels[tmhid]}_{$sequence}" >
        <span>Avg Price: {$approved_hotels['avgPrice']} {$currency_dispname}</span>
        <input type='hidden' id='avgprice_segacc_{$approved_hotels[tmhid]}_{$sequence}' value='{$approved_hotels['avgPrice']}'/>
    </div>
    <br>
    <div style="display:inline-block;padding:5px;width:15%;">{$lang->pricenight}</div>
    <div style="display:inline-block;width:15%;"><input tabindex="3"  id="pricenight_segacc_{$approved_hotels[tmhid]}_{$sequence}" accept="numeric" name="segment[{$sequence}][tmhid][{$checksum}][priceNight]" type="text" value="{$selectedhotel->priceNight}" style="width:100%;"></div>
    <div style="display:inline-block;width:6%;">{$currencies_list}</div>
    <div style="display:inline-block;padding:10px;margin-left:5px;width:16%;">{$lang->numnight}</div>
    <div style="display:inline-block;width:15%;"><input style="width:100px;" tabindex="3" id="numnight_segacc_{$approved_hotels[tmhid]}_{$sequence}" accept="numeric" name="segment[{$sequence}][tmhid][{$checksum}][numNights]" type="number" value="{$selectedhotel->numNights}" max="{$leavedays}"> </div>
    <div style="display:inline-block;padding:2px;width:20%; font-weight: bold;" id="total_segacc_{$approved_hotels[tmhid]}_{$sequence}" >
        <span>Sub Total: {$selectedhotel->total} </span>
    </div>
    <div id='hotelprice_warning_{$approved_hotels[tmhid]}_{$sequence}'></div>
    <input type="hidden"  disabled value="{$selectedhotel->total}" id="hotel_{$sequence}_{$checksum}_total}" name="segment[{$sequence}][tmhid][{$checksum}][subtotal]">
    <input type="hidden" disabled value="{$checksum}" id="checksum_{$approved_hotels[tmhid]}_{$sequence}_{$checksum}_tmhid">
    <div>
        <div style="display:inline-block;padding:5px;width:15%;">{$lang->paidby}</div> <div style="display:inline-block;width:20%;">{$selectlists[paidBy]}</div><div class="ui-state-highlight ui-corner-all" style="margin-left:100px;padding-left: 5px; margin-bottom:10px;display:inline-block;width:20%;">{$lang->maxnight} : {$leavedays} </div>
    </div>
    <div id="anotheraff_accomodations_{$sequence}_{$checksum}" style="{$selected_hotel[$sequence][$checksum][displaystatus]} padding: 5px;" class="border_bottom border_left border_right border_top" >
        <div style="display:inline-block;width:15%;">{$lang->anotheraff}</div>
        <div style="display:inline-block;width:20%;padding:5px;"><input id="affiliate_{$sequence}_{$approved_hotels[tmhid]}_cache_acc_autocomplete" autocomplete="false" tabindex="3" value="{$affiliate->name}" type="text"></div>
        <input id="affiliate_{$sequence}_{$approved_hotels[tmhid]}_cache_acc_id" name="segment[{$sequence}][tmhid][{$checksum}][paidById]"  value="{$selectedhotel->paidById}" type="hidden">
    </div>
</div>
