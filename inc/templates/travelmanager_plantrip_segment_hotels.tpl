<div style="display:block;width:100%;">
    <div style="display:inline-block;width:20%;" >{$checkbox_hotel}<span>{$review_tools}</span></div>
    <div style="display:inline-block;padding:5px;width:40%;">{$lang->pricenight} <input  id="pricenight_segacc_{$approved_hotels[tmhid]}_{$sequence}" accept="numeric" name="segment[{$sequence}][tmhid][$approved_hotels[tmhid]][priceNight]" type="text" value="{$selectedhotel[$segid][$approved_hotels[tmhid]][priceNight]}"> </div>
    <div style="display:inline-block;padding:5px;width:30%;">{$lang->numnight} <input  size="10" id="numnight_segacc_{$approved_hotels[tmhid]}_{$sequence}" accept="numeric" name="segment[{$sequence}][tmhid][$approved_hotels[tmhid]][numNights]" type="text" value="{$selectedhotel[$segid][$approved_hotels[tmhid]][numNights]}"> </div>
    <div style="display:inline-block;padding:2px;width:30%; display:block; font-weight: bold;" id="total_segacc_{$approved_hotels[tmhid]}_{$sequence}" ><span>Sub Total : USD {$selectedhotel[$segid][$approved_hotels[tmhid]][total]} </span></div>
    <div style="display:inline-block;padding:0px;width:40%; display:block;"  > {$paidby_details} </div>

    <div style="{$selectedhotel[$segid][$approved_hotels[tmhid]][display]} padding: 8px;" class="border_bottom border_left border_right border_top" id="anotheraff_{$sequence}_{$approved_hotels[tmhid]}">
        <span>Another Affiliate </span>
        <input id="affiliate_{$sequence}_{$approved_hotels[tmhid]}_cache_autocomplete" autocomplete="off" tabindex="8" value="{$selectedhotel[$segid][$approved_hotels[tmhid]][affiliate]}"  type="text">
        <input id="affiliate_{$sequence}_{$approved_hotels[tmhid]}_cache_id" name="segment[{$sequence}][tmhid][$approved_hotels[tmhid]][paidBy]"    value="{$selectedhotel[$segid][$approved_hotels[tmhid]][affid]}"type="hidden">
    </div>
</div>