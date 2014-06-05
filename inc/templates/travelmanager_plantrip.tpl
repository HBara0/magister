<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->requestleave}</title>
        {$headerinc}
        <script>
            $(function() {
                var tabs = $("#segmentstabs").tabs();
                var tabcounter = tabs.find(".ui-tabs-nav").length + 1;
                $("#createtab").live('click', function() {
                    var id = "segmentstabs-" + tabcounter;
                    var label = "segment " + tabcounter;
                    // tabTemplate = "<li><a href='#" + id + "'>" + label + "</a></li>"
                    tabTemplate = "<li><a href='#" + id + "'>" + label + "</a> <span class='ui-icon ui-icon-close' role='presentation' title='Close'>Remove Tab</span></li>"

                    tabs.find(".ui-tabs-nav").append(tabTemplate);
                    /* get content thought ajax*/
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }
                    /*Select the  tabs-panel that isn't hidden with  tabs-hide:*/
                    var selectedPanel = $("#segmentstabs div.ui-tabs-panel:not(.ui-tabs-hide)");

                    var templatecontent = sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=add_segment", "sequence=" + tabcounter + "&lid=" + $('#lid').val() + "&destcity=" + $('#destinationcity_' + (tabcounter - 1) + '_cache_id').val() + "&toDate=" + $('#altpickDate_to_' + (tabcounter - 1)).val(), 'loadindsection', id, id, true);

                    tabs.append("<div id=" + id + "><p>" + templatecontent + "</p></div>");
                    tabs.tabs("refresh");
                    $("#segmentstabs").tabs("option", "active", (tabcounter) - 1);
                    tabcounter = tabcounter + 1;

                });
                // close icon: removing the tab on click
                tabs.delegate("span.ui-icon-close", "click", function() {
                    var panelId = $(this).closest("li").remove().attr("aria-controls");
                    $("#" + panelId).remove();
                    tabs.tabs("refresh");
                });
                $('input[id^=destinationcity_]').live('change', function() {
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }
                    var id = $(this).attr('id').split("_");
                    var sequence = id[1];
                    var ciid = $('input[id$=destinationcity_' + sequence + '_cache_id]').val(); /*get  the cityid from the hiiden field*/
                    var origincity = $('input[id=cities_' + sequence + '_cache_id]').val(); /*get  the cityid from the hiiden field*/

                    sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=populatecontent", "&sequence=" + sequence + "&destcity=" + ciid + "&origincity=" + origincity + "&departuretime=" + $('#altpickDate_to_' + (sequence - 1)).val(), 'content_detailsloader_' + sequence + '', 'content_details_' + sequence + '', true);
                    sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=populatecityprofile", "&sequence=" + sequence + "&destcity=" + ciid, 'segment_city_loader_' + sequence + '', 'segment_city_' + sequence + '', true);


                });
                $('input[id*=transp_]').live('click', function() {
                    var id = $(this).attr('id').split("_");
                    var sequence = id[1];
                    /*ajax call to parse transpfields*/
                    sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=parsedetailstransp", "&sequence=" + sequence + "&catid=" + id[2], 'cat_detailsloader', 'cat_content', true);
                });
            });

        </script>
        <style type="text/css">
            .ui-tabs-nav li {
                width:110px;
            }

            .ui-icon,.ui-icon-close {
                cursor: pointer;
                title
            }
        </style>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <form name="perform_travelmanager/plantrip_Form" id="perform_travelmanager/plantrip_Form" action="#" method="post">
                <input type="hidden" value="{$leaveid}" id="lid" name="lid"/>{$tools_addnewtab}
                <input type="hidden" value="{$sequence}" id="lid" name="sequence"/>
                <input type="hidden" value="{$previoussegtodate}" id="todate" name="todate"/>
                <input type="hidden" value="{$previoussegdestcity}" id="prevdestcity" name="prevdestcity"/>
                <div id="segmentstabs">
                    <ul>
                        <li><a href="#segmentstabs-1">Segment 1</a></li>
                    </ul>
                    <div id="loadindsection">  </div>
                    {$segments_output}
                </div>
                <input type='submit' class='button' value="{$lang->savecaps}" id='perform_travelmanager/plantrip_Button'>
                <input type='submit'  class='button' value="{$lang->continue}" name="continue"  >
            </form>
            <div id="perform_travelmanager/plantrip_Results"></div>
        </td>
    </tr>
    {$footer}
</body>

</html>