{$selectlists[paidby]}
<div style="{$transportation_details[$sequence][$category[inputChecksum]][display]} padding: 10px;" id="anotheraff_transp_{$category[inputChecksum]}_{$flight[flightid]}_{$sequence}">
    <div style="display:inline-block;width:25%;padding:5px;">{$lang->anotheraff}</div>
    <div style="display:inline-block;width:25%;"><input id="affiliate_{$sequence}_cache_{$category[inputChecksum]}_{$flight[flightid]}_autocomplete" autocomplete="off" tabindex="8"  value="{$transportation_details[$sequence][$category['inputChecksum']]['affiliate']}" type="text" style="width:100%;"></div>
    <input id="affiliate_{$sequence}_cache_{$category[inputChecksum]}_{$flight[flightid]}_id" name="segment[{$sequence}][tmtcid][{$category[inputChecksum]}][{$flight[flightid]}][paidById]"  value="{$transportation_details[$sequence][$category[inputChecksum]][affid]}" type="hidden">
</div>