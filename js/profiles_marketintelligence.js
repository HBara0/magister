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
});