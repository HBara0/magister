<div style="{$drivingmode['transpcat']['display']}; width:100%; margin-bottom:20px;" id="cat_content_{$drivingmode[transpcat][cateid]}_{$sequence}">
    <h3>{$drivingmode[transpcat][title]}</h3>
    <div style="display:inline-block;width:80%;vertical-align: top">
        {$transp_category_fields}
        <div style="{$transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']]['display']} padding: 8px;" id="anotheraff_transp_{$drivingmode['transpcat']['cateid']}_{$sequence}">
            <span>{$lang->anotheraffiliate}</span>
            <input id="affiliate_{$sequence}_cache_{$drivingmode[transpcat][cateid]}_autocomplete" autocomplete="off" tabindex="8"  value="{$transportation_details[$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']][affiliate]}"  type="text">
            <input id="affiliate_{$sequence}_cache_{$drivingmode[transpcat][cateid]}_id" name="segment[{$sequence}][tmtcid][{$drivingmode[transpcat][cateid]}][paidById]"  value="{$transportation_details[$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']][affid]}" type="hidden">
        </div>
    </div>
    {$todelete[$drivingmode[transpcat][cateid]]}
    {$possible_transportation}
</div>
