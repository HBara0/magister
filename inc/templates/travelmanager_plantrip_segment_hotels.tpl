<div style="display:block;width:100%;  border:1px solid #FCEFA1;  padding:5px;">
    <div><input aria-describedby="ui-tooltip-155" title="" name="segment[{$sequence}][tmhid][{$checksum}][tmhid]" id="segment[{$sequence}][{$checksum}][tmhid]" value="{$hotel->tmhid}" type="checkbox" {$hotel->isChecked}> $hotel->name<span>{$review_tools}</span></div>
    <div>
        <div style="display:inline-block;padding:5px;width:15%;">{$lang->pricenight}</div>
        <div style="display:inline-block;width:20%;"><input id="pricenight_segacc_{$approved_hotels[tmhid]}_{$sequence}" accept="numeric" name="segment[{$sequence}][tmhid][{$checksum}][priceNight]" type="text" value="{$selectedhotel->priceNight}" style="width:100%;"></div>
        <div style="display:inline-block;padding:10px;width:20%;">{$lang->numnight}</div>
        <div style="display:inline-block;width:15%;"><input size="10" id="numnight_segacc_{$approved_hotels[tmhid]}_{$sequence}" accept="numeric" name="segment[{$sequence}][tmhid][{$checksum}][numNights]" type="text" value="{$selectedhotel->numNights}"> </div>
        <div style="display:inline-block;padding:2px;width:20%; font-weight: bold;" id="total_segacc_{$approved_hotels[tmhid]}_{$sequence}" >
            <span>Sub Total: {$selectedhotel->total} </span>
        </div>
    </div>
    <div>
        <div style="display:inline-block;padding:5px;width:15%;">{$lang->currency}</div>
        <div style="display:inline-block;width:20%;">{$currencies_list}</div>
    </div>

    <div>
        <div style="display:inline-block;padding:5px;width:15%;">{$lang->paidby}</div> <div style="display:inline-block;width:20%;">{$selectlists[paidBy]}</div>
    </div>
    <div id="anotheraff_accomodations_{$sequence}_{$hotel->tmhid}" style="{$selectedhotel->displayStatus} padding: 5px;" class="border_bottom border_left border_right border_top" >
        <div style="display:inline-block;width:15%;">{$lang->anotheraff}</div>
        <div style="display:inline-block;width:20%;padding:5px;"><input id="affiliate_{$sequence}_{$approved_hotels[tmhid]}_cache_acc_autocomplete" autocomplete="off" tabindex="8" value="{$selectedhotel->affiliate->name}" type="text"></div>
        <input id="affiliate_{$sequence}_{$approved_hotels[tmhid]}_cache_acc_id" name="segment[{$sequence}][tmhid][{$checksum}][paidBy]"  value="{$selectedhotel->paidBy}" type="hidden">
    </div>
</div>
