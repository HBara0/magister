/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: marketintelligence.js
 * Created:        @zaher.reda    Jun 11, 2014 | 11:31:51 AM
 * Last Update:    @zaher.reda    Jun 11, 2014 | 11:31:51 AM
 */
$(function() {
    $('div[id^="markettimeline_"][class*="circle_clickable"]').live('click', function() {
        var id = $(this).attr("id").split('_');

        if($.trim($('div[id^="previoustimelinecontainer_' + id[1] + '"]').html()).length > 0) {
            $('div[id^="previoustimelinecontainer_' + id[1] + '"]').slideToggle("slow");
            return false;
        }

        sharedFunctions.requestAjax("post", "index.php?module=profiles/entityprofile&action=parse_previoustimeline", "miprofile=" + $(this).parents("div").children("input[id^='miprofile-']").val() + "&tlrelation=" + $(this).parents("div").children("input[id^='tlrelation-']").val(), 'previoustimelinecontainer_' + id[1], 'previoustimelinecontainer_' + id[1], true, 'animate');
    });

    $('input[id="mktshareperc"]').live('keyup', function() {
        if(!jQuery.isNumeric($('input[id="mktshareperc"]').val())) {
            return;
        }
        if($(this).val().length > 0 && $('input[id=potential]').val().length > 0) {
            $('input[id="mktshareqty"]').val(Number($('input[id="potential"]').val()) * $(this).val() / 100);
        }
    });

    $('input[id="mktshareqty"]').live('keyup', function() {
        if($('input[id="potential"]').val().length > 0) {
            $('input[id="mktshareperc"]').val($(this).val() / ($('input[id="potential"]').val()) * 100);
            //$('input[id="mktshareperc"]').trigger('keyup');
        }
    });
    /*parse end product type*/
    $("input[id='customer_1_autocomplete']").live('blur', function() {
        var cid = $('input[id="customer_1_id"]').val();
        if(cid.length == 0) {
            return;
        }
        var data = "&action=get_entityendproduct&eid=" + cid;
        sharedFunctions.requestAjax("post", $(location).attr('href'), data, 'entitiesbrandsproducts_endproductResult', 'entitiesbrandsproducts_endproductResult', 'html');
    });
});