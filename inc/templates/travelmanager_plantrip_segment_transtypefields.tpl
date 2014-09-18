<div style="display:none; width:100%; margin-bottom:20px;" id="cat_content_{$drivingmode[transpcat][cateid]}">
    <h3>{$drivingmode[transpcat][title]}</h3>
    {$transp_category_fields}
    <div style="display:none; padding: 8px;" id="anotheraff">
        <span>Another Affiliate </span>
        <input id="affiliate_{$sequence}_cache_autocomplete" autocomplete="off" tabindex="8" value=""  type="text">
        <input id="affiliate_{$sequence}_cache_id" name="segment[{$sequence}][tmtcid][paidBy]" value="" type="hidden">
    </div>
    {$possible_transportation}
</div>