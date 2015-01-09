<div style="display:block;width:100%;border:1px solid #FCEFA1;  padding:5px; ">
    <div style="display:inline-block; " >{$checkbox_hotel}<span>{$review_tools}</span></div>
    <div style="display:inline-block;padding:5px;">{$lang->pricenight} <input  id="pricenight_segacc_{$approved_hotels[tmhid]}_{$sequence}" accept="numeric" name="segment[{$sequence}][tmhid][$approved_hotels[tmhid]][priceNight]" type="text" value="{$selectedhotel[$segid][$approved_hotels[tmhid]][priceNight]}"> </div>
    <div style="display:inline-block;padding:5px; ">{$lang->numnight} <input  size="10" id="numnight_segacc_{$approved_hotels[tmhid]}_{$sequence}" accept="numeric" name="segment[{$sequence}][tmhid][$approved_hotels[tmhid]][numNights]" type="text" value="{$selectedhotel[$segid][$approved_hotels[tmhid]][numNights]}"> </div>
    <div style="display:inline-block;padding:2px;width:30%; font-weight: bold;" id="total_segacc_{$approved_hotels[tmhid]}_{$sequence}" ><span>Sub Total:{$selectedhotel[$segid][$approved_hotels[tmhid]][currency]}  {$selectedhotel[$segid][$approved_hotels[tmhid]][total]} </span></div>
    <div style="display:inline-block;padding:0px;">{$lang->currency} {$currencies_list}</div>
    <div style="display:inline-block;padding:0px;"> {$paidby_details} </div>

    <div id="anotheraff_accomodations_{$sequence}_{$approved_hotels[tmhid]}" style="{$selectedhotel[$segid][display]} padding: 8px;" class="border_bottom border_left border_right border_top">
        <span>Another Affiliate </span>
        <input id="affiliate_{$sequence}_{$approved_hotels[tmhid]}_cache_acc_autocomplete" autocomplete="off" tabindex="8" value="{$selectedhotel[$segid][$approved_hotels[tmhid]][affiliate]}"  type="text">
        <input id="affiliate_{$sequence}_{$approved_hotels[tmhid]}_cache_acc_id" name="segment[{$sequence}][tmhid][$approved_hotels[tmhid]][paidBy]"    value="{$selectedhotel[$segid][$approved_hotels[tmhid]][affid]}" type="hidden">
    </div>

</div>
