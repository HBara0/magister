<div style="display:block;width:100%; margin-top:15px;margin-bottom :15px; padding:5px; border:1px solid #FCEFA1;">
    <div style="display:inline-block; " class="subtitle">{$lang->addotherhotel}</div>
    <div style="display:block; ">
        <input id="hotels_{$sequence}_cache_hotel_autocomplete" autocomplete="off" tabindex="8" placeholder="{$lang->searchotherhotel}" value="{$selectedhotel[$segid][$approved_hotels[tmhid]][$approved_hotels[tmhid]]}"  type="text">
        <input id="hotels_{$sequence}_cache_hotel_id" name="segment[{$sequence}][tmhid][{$otherhotel_checksum}][tmhid]" value="" type="hidden">


        <span> <a style="cursor: pointer;"  rel="{$destcity[ciid]}" id="addnewhotel_{$sequence}_travelmanager/plantrip_loadpopupbyid"><img src="images/addnew.png"  title="{$lang->addotherhotel}" alt="Add" border="0"></a> </span>
        <br/>
        <div style="display:inline-block;padding:5px;width:15%;">{$lang->pricenight}</div>
        <div style="display:inline-block;padding:5px;width:20%;"><input  id="pricenight_segacc_{$approved_hotels[tmhid]}_{$sequence}" accept="numeric" name="segment[{$sequence}][tmhid][{$otherhotel_checksum}][priceNight]" type="text" value="{$selectedhotel[$segid][$approved_hotels[tmhid]][priceNight]}"> </div>
        <div style="display:inline-block;padding:5px;width:20%; ">{$lang->numnight}</div>
        <div style="display:inline-block;padding:5px;width:15%;"> <input  size="10" id="numnight_segacc_{$approved_hotels[tmhid]}_{$sequence}" accept="numeric" name="segment[{$sequence}][tmhid][{$otherhotel_checksum}][numNights]" type="text" value="{$selectedhotel[$segid][$approved_hotels[tmhid]][numNights]}"> </div>
        <div style="display:inline-block;padding:2px;  font-weight: bold; width:15%;" id="total_segacc_{$approved_hotels[tmhid]}_{$sequence}" ><span>Sub Total: {$selectedhotel[$segid][$approved_hotels[tmhid]][total]} </span></div>
        <div style="display:inline-block;padding:5px;width:15%;">{$lang->currency}</div> <div style="display:inline-block;padding:5px;width:20%;">{$currencies_list}</div>
        <div>
            <div style="display:inline-block;padding:5px;width:15%">{$lang->paidby}</div>
            <div style="display:inline-block;width:20%;">
                <select id="paidbylist_accomodations_{$sequence}" name="segment[{$sequence}][tmhid][{$otherhotel_checksum}][entites]"><option value="myaffiliate"> My Affiliate </option>
                    <option value="supplier"> Supplier </option>
                    <option value="client"> Client  </option>
                    <option value="myself"> Myself  </option>
                    <option value="anotheraff"> Another Affiliate </option>
                </select>
            </div>
        </div>

        <div id="anotheraff_accomodations_{$sequence}_{$approved_hotels[tmhid]}" style=" padding: 8px;" class="border_bottom border_left border_right border_top">
            <div style="display:inline-block;width:15%;">{$lang->anotheraff}</div>
            <div style="display:inline-block;width:20%;padding:5px;"><input id="affiliate_{$sequence}_{$approved_hotels[tmhid]}_cache_otheracc_autocomplete" autocomplete="off" tabindex="8" value="{$selectedhotel[$segid][$approved_hotels[tmhid]][affiliate]}"  type="text"></div>
            <input id="affiliate_{$sequence}_{$approved_hotels[tmhid]}_cache_otheracc_id" name="segment[{$sequence}][tmhid][{$otherhotel_checksum}][paidBy]"    value="{$selectedhotel[$segid][$approved_hotels[tmhid]][affid]}" type="hidden">
        </div>

    </div>

</div>
