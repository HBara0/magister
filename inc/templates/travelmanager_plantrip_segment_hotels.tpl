<div style="display:block;width:100%;border:1px solid #FCEFA1;  padding:5px; ">
    <div >{$checkbox_hotel}<span>{$review_tools}</span></div>
    <div>
        <div style="display:inline-block;padding:5px;width:15%;">{$lang->pricenight}</div>
        <div style="display:inline-block;width:20%;"><input id="pricenight_segacc_{$approved_hotels[tmhid]}_{$sequence}" accept="numeric" name="segment[{$sequence}][tmhid][$approved_hotels[tmhid]][priceNight]" type="text" value="{$selectedhotel[$segid][$approved_hotels[tmhid]][priceNight]}" style="width:100%;"></div>
        <div style="display:inline-block;padding:10px;width:20%;">{$lang->numnight}</div>
        <div style="display:inline-block;width:15%;"><input size="10" id="numnight_segacc_{$approved_hotels[tmhid]}_{$sequence}" accept="numeric" name="segment[{$sequence}][tmhid][$approved_hotels[tmhid]][numNights]" type="text" value="{$selectedhotel[$segid][$approved_hotels[tmhid]][numNights]}"> </div>
        <div style="display:inline-block;padding:2px;width:20%; font-weight: bold;" id="total_segacc_{$approved_hotels[tmhid]}_{$sequence}" >
            <span>Sub Total:{$selectedhotel[$segid][$approved_hotels[tmhid]][currency]}  {$selectedhotel[$segid][$approved_hotels[tmhid]][total]} </span></div>
    </div>
    <div>
        <div style="display:inline-block;padding:5px;width:15%;">{$lang->currency}</div>
        <div style="display:inline-block;width:20%;">{$currencies_list}</div>
    </div>

    <div>{$paidby_details}</div>
    <div id="anotheraff_accomodations_{$sequence}_{$approved_hotels[tmhid]}" style="{$selectedhotel[$segid][display]} padding: 5px;" class="border_bottom border_left border_right border_top" >
        <div style="display:inline-block;width:15%;">Another Affiliate </div>
        <div style="display:inline-block;width:20%;padding:5px;"><input id="affiliate_{$sequence}_{$approved_hotels[tmhid]}_cache_acc_autocomplete" autocomplete="off" tabindex="8" value="{$selectedhotel[$segid][$approved_hotels[tmhid]][affiliate]}"  type="text"></div>
        <input id="affiliate_{$sequence}_{$approved_hotels[tmhid]}_cache_acc_id" name="segment[{$sequence}][tmhid][$approved_hotels[tmhid]][paidBy]"    value="{$selectedhotel[$segid][$approved_hotels[tmhid]][affid]}" type="hidden">
    </div>

</div>
