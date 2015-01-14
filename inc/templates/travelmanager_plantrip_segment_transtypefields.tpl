<tr {$row_id}><td>
        <div style="{$drivingmode['transpcat']['display']}; width:100%; margin-bottom:20px;" id="cat_content_{$drivingmode[transpcat][cateid]}_{$sequence}">
            <h3>{$drivingmode[transpcat][title]}</h3>

            {$transp_category_fields}

            <div style="display:inline-block;width:25%;padding:10px;">{$todelete[$drivingmode[transpcat][cateid]]}</div>
            <div style="{$transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']]['display']} padding: 10px;" id="anotheraff_transp_{$drivingmode['transpcat']['cateid']}_{$sequence}">
                <div style="display:inline-block;width:25%;padding:5px;">{$lang->anotheraffiliate}</div>
                <div style="display:inline-block;width:25%;"><input id="affiliate_{$sequence}_cache_{$drivingmode[transpcat][cateid]}_autocomplete" autocomplete="off" tabindex="8"  value="{$transportation_details[$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']][affiliate]}"  type="text" style="width:100%;"></div>
                <input id="affiliate_{$sequence}_cache_{$drivingmode[transpcat][cateid]}_id" name="segment[{$sequence}][tmtcid][{$drivingmode[transpcat][cateid]}][paidById]"  value="{$transportation_details[$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']][affid]}" type="hidden">
            </div>


            {$othercategories}
            <!-- </div>-->
            {$possible_transportation}
        </div>
    </td></tr>