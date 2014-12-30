<div style="{$drivingmode['transpcat']['display']}; width:100%; margin-bottom:20px;" id="cat_content_{$drivingmode[transpcat][cateid]}">
    <h3>{$drivingmode[transpcat][title]}</h3>
    {$transp_category_fields}
    <div style="{$transportation_details[$segmentid][$transp->tmtcid][display]}; padding: 8px;" id="anotheraff_transp_{$drivingmode['transpcat']['cateid']}_{$sequence}">
        <span>Another Affiliate </span>
        <input id="affiliate_{$sequence}_cache_{$drivingmode[transpcat][cateid]}_autocomplete" autocomplete="off" tabindex="8"  value="{$transportation_details[$segmentid][$transp->tmtcid][affiliate]}"  type="text">
        <input id="affiliate_{$sequence}_cache_{$drivingmode[transpcat][cateid]}_id" name="segment[{$sequence}][tmtcid][{$drivingmode[transpcat][cateid]}][paidById]"  type="hidden">


    </div>
    {$possible_transportation}
</div>
