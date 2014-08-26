<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->requestleave}</title>
        {$headerinc}
        <script>
            $(function() {

                var tabs = $("#segmentstabs").tabs();
                var tabcounter = tabs.find(".ui-tabs-nav").length + 1;
                $("#createtab").live('click', function() {
                    var templatecontent = errormessage = '';
                    var id = "segmentstabs-" + tabcounter;
                    /*User cannot add a new segment if the destination city/to date of the previous segment are not filled*/
                    if($('#altpickDate_to_' + (tabcounter - 1)).val() == '' || ($('#destinationcity_' + (tabcounter - 1) + '_cache_id').val() == '')) {
                        var errormessage = ' Please make sure the to Date and Destination city are filled ';
                        tabs.append("<div id=" + id + "><p class='red_text'>" + errormessage + "</p></div>");
                        varerrormessage = '';
                        return false;
                    }
                    else {
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
                        var templatecontent = sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=add_segment", "sequence=" + tabcounter + "&lid=" + $('#lid').val() + "&destcity=" + $('#destinationcity_' + (tabcounter - 1) + '_cache_id').val() + "&toDatetime=" + (Date.parse($('#pickDate_to_' + (tabcounter - 1)).val())) + "&leavetoDatetime=" + $('#leaveDate_to_' + (tabcounter - 1)).val() + "&toDate=" + $('#altpickDate_to_' + (tabcounter - 1)).val(), 'loadindsection', id, id, true);
                        var templatecontent = errormessage = '';
                        tabs.append("<div id=" + id + "><p>" + templatecontent + "</p></div>");
                        tabs.tabs("refresh");
                        $("#segmentstabs").tabs("option", "active", (tabcounter) - 1);
                        tabcounter = tabcounter + 1;
                    }

                });
                // close icon: removing the tab on click
                tabs.delegate("span.ui-icon-close", "click", function() {
                    var panelId = $(this).closest("li").remove().attr("aria-controls");
                    $("#" + panelId).remove();
                    tabcounter = tabcounter - 1;
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

                /*var firstcategoryid = $('input[id*=transp_]').attr('id').split("_")[3];*/

                $('input[id*=transp_]').live('click', function() {
                    var id = $(this).attr('id').split("_");
                    var sequence = id[1];
                    var categoryid = id[3];
                    $('div[id=cat_content_' + categoryid + ']').slideToggle("slow");
                    /*ajax call to parse transpfields*/
                    //  sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=parsedetailstransp", "&categoryid=" + categoryid + "&sequence=" + sequence + "&catid=" + id[2], 'cat_detailsloader_' + categoryid + '', 'transpcat_content' + categoryid + '', true);

                });
                $('input[id*=pickDate_to_]').live('change', function() {
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }
                    var todate = $("#altpickDate_to_" + (tabcounter - 1)).val();
                    var fromdate = $("#altpickDate_from_" + (tabcounter - 1)).val();
                    // end - start returns difference in milliseconds
                    var diff = new Date(todate - fromdate);
                    // get days
                    var days = diff / 1000 / 60 / 60 / 24;
                    // $('#numdays_' + (tabcounter - 1)).html(days);

                });
                $("input[id^='pickDate']").each(function() {
                    //$(this).datepicker("option", "maxDate", new Date($("#pickDate_to_" + (tabcounter - 1)).val()));

                });
            });

        </script>
        <style type="text/css">
            .ui-tabs-nav li {
                width:110px;
            }

            .ui-icon,.ui-icon-close {
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->plantrip}Plan Trip</h1>
            <div class="ui-state-highlight ui-corner-all" style='padding: 5px; font-style: italic;'>{$leave['type_output']} - {$leave[fromDate_output]} -  {$leave[toDate_output]}</div>
            <form name="perform_travelmanager/plantrip_Form" id="perform_travelmanager/plantrip_Form" action="#" method="post">
                <div style='margin-top: 10px;'>
                    <a id="createtab" class="showpopup" href="#"><img border="0" alt="{$lang->addsegment}" src="images/addnew.png"> {$lang->addsegment}</a>
                </div>
                <input type="hidden" value="{$sequence}" name="sequence"/>
                <input type="hidden" value="{$previoussegtodate}" id="todate" name="todate"/>
                <input type="hidden" value="{$previoussegdestcity}" id="prevdestcity" name="prevdestcity"/>
                <input type="hidden" value="{$leaveid}" id="lid" name="lid"/>
                <div id="segmentstabs">
                    <ul>
                        <li><a href="#segmentstabs-1">Segment 1</a></li>
                    </ul>
                    <div id="loadindsection"></div>
                    {$segments_output}
                </div>
                <input type='submit' class='button' value="{$lang->savecaps}" id='perform_travelmanager/plantrip_Button'>
            </form>
            <div id="perform_travelmanager/plantrip_Results"></div>
        </td>
    </tr>
    {$footer}
</body>
</html>