<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->requestleave}</title>
        {$headerinc}
        <script>
            $(function () {
                var tabs = $("#segmentstabs").tabs();
                var tabcounter = tabs.find(".ui-tabs-nav").find('li').length + 1; //find the  lenght of li tabs and increment by 1
                $(document).on('click', "#createtab", function () {
                    var templatecontent = errormessage = '';
                    var id = "segmentstabs-" + tabcounter;
                    /*User cannot add a new segment if the destination city/to date of the previous segment are not filled*/
                    if($('#pickDate_to_' + (tabcounter - 1)).val() == '' || ($('#destinationcity_' + (tabcounter - 1) + '_cache_id').val() == '')) {
                        var errormessage = ' Please make sure the to Date and Destination city are filled ';
                        tabs.append("<div id=" + id + "><p class='red_text'>" + errormessage + "</p></div>");
                        varerrormessage = '';
                        return false;
                    }
                    else {
                        $('div[id=segmentstabs-' + tabcounter + ']').remove(); //remove  Error about destination city and date
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
                        var templatecontent = sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=add_segment", "sequence=" + tabcounter + "&lid=" + $('#lid').val() + "&destcity=" + $('#destinationcity_' + (tabcounter - 1) + '_cache_id').val() + "&toDate=" + ($('#pickDate_to_' + (tabcounter - 1)).val()) + "&fromDate=" + ($('#pickDate_from_' + (tabcounter - 1)).val()) + "&leavetoDatetime=" + $('#leaveDate_to_' + (tabcounter - 1)).val() + "&toDate=" + $('#altpickDate_to_' + (tabcounter - 1)).val(), 'loadindsection', id, 'html', true);
                        var templatecontent = errormessage = '';
                        tabs.append("<div id=" + id + "><p>" + templatecontent + "</p></div>");
                        tabs.tabs("refresh");
                        $("#segmentstabs").tabs("option", "active", (tabcounter) - 1);
                        tabcounter = tabcounter + 1;
                    }

                });
                $(document).on('click', 'input[id="save_addsegment"]', function () {
                    //  setTimeout('alert("www")', 2000);
                    function click_seg() {
                        $('a[id="createtab"]').click();
                    }
                    $('input[id="saveaddseg"]').val("{$sequence}");
                    $('input[id="perform_travelmanager/plantrip_Button"]').click();
                    setTimeout(click_seg, 2000);
                    $('input[id="saveaddseg"]').val(0);
                });
                // close icon: removing the tab on click
                tabs.delegate("span.ui-icon-close", "click", function () {
                    /*only send ajax request when segmentid exist on modify*/
                    if(typeof $(this).closest("li").find('span').attr('id') !== typeof undefined && $(this).closest("li").find('span').attr('id') !== false) {
                        var segmentid = $(this).closest("li").find('span').attr('id').split("_");
                        sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=deletesegment", "&segmentid=" + segmentid[1], '', '', true);
                    }
                    if($('#pickDate_to_' + (tabcounter - 1)).val() != '0' || $('#pickDate_to_' + (tabcounter - 1)).val() != '') {
                        $('#pickDate_to_' + (tabcounter - 2)).val($('#pickDate_to_' + (tabcounter - 1)).val());
                        $('input[id="altpickDate_to_' + (tabcounter - 2) + '"]').val($('input[id="altpickDate_to_' + (tabcounter - 1) + '"]').val());
                    }
                    var panelId = $(this).closest("li").remove().attr("aria-controls");
                    $("#" + panelId).remove();
                    tabcounter = tabcounter - 1;
                    tabs.tabs("refresh");
                    $('input[id="perform_travelmanager/plantrip_Button"]').click();
                });
                $(document).on('change', 'input[id^=destinationcity_],input[id^=pickDate_to_]', function () {
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }
                    var id = $(this).attr('id').split("_");
                    if(id[0] == 'destinationcity') {
                        var sequence = id[1];
                    }
                    else if(id[0] == 'pickDate') {
                        var sequence = id[2];
                    }
                    errormessage = '';
                    var ciid = $('input[id$=destinationcity_' + sequence + '_cache_id]').val(); /*get  the cityid from the hiiden field*/
                    if(typeof ciid !== typeof undefined && ciid !== '') {
                        var origincity = $('input[id=cities_' + sequence + '_cache_id]').val(); /*get  the cityid from the hiiden field*/
                        sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=populatecontent", "&sequence=" + sequence + "&destcity=" + ciid + "&origincity=" + origincity + "&departuretime=" + $('#altpickDate_from_' + sequence).val() + "&arrivaltime=" + $('#altpickDate_to_' + sequence).val(), 'content_detailsloader_' + sequence + '', 'content_details_' + sequence + '', true);
                        sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=populatecityprofile", "&sequence=" + sequence + "&destcity=" + ciid, 'segment_city_loader_' + sequence + '', 'segment_city_' + sequence + '', true);

                    }

                });

                $(document).on('click', 'input[id^=lookuptransps_]', function () {
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }
                    var id = $(this).attr('id').split("_");
                    var sequence = id[1];
                    errormessage = '';
                    var ciid = $('input[id$=destinationcity_' + sequence + '_cache_id]').val(); /*get  the cityid from the hiiden field*/
                    if(typeof ciid !== typeof undefined && ciid !== '') {
                        var origincity = $('input[id=cities_' + sequence + '_cache_id]').val(); /*get  the cityid from the hiiden field*/
                        var transp = 0;
                        if($('input[id=transp_lookuptransps_' + sequence + ']:checked').val().length > 0) {
                            transp = $('input[id=transp_lookuptransps_' + sequence + ']:checked').val();
                        }
                        sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=populatecontent", "&sequence=" + sequence + "&parsetransp=1" + "&destcity=" + ciid + "&origincity=" + origincity + "&departuretime=" + $('#altpickDate_from_' + sequence).val() + "&arrivaltime=" + $('#altpickDate_to_' + sequence).val() + '&transp=' + transp, 'content_detailsloader_' + sequence + '', 'content_details_' + sequence + '', true);
                        sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=populatecityprofile", "&sequence=" + sequence + "&destcity=" + ciid, 'segment_city_loader_' + sequence + '', 'segment_city_' + sequence + '', true);
                    }
                });
                /*var firstcategoryid = $('input[id*=transp_]').attr('id').split("_")[3];*/

                $(document).on('click', 'input[id*=transp_]', function () {
                    var id = $(this).attr('id').split("_");
                    var sequence = id[1];
                    var categoryid = id[3];
                    $('div[id=cat_content_' + categoryid + '_' + sequence + ']').slideToggle("slow");
                    /*ajax call to parse transpfields*/
                    //  sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=parsedetailstransp", "&categoryid=" + categoryid + "&sequence=" + sequence + "&catid=" + id[2], 'cat_detailsloader_' + categoryid + '', 'transpcat_content' + categoryid + '', true);

                });
                $(document).on('click', 'a[id^=countryhotels_][id$=_check]', function () {
                    var id = $(this).attr('id').split("_");
                    var sequence = id[1];
                    $('div[id=countryhotels_' + sequence + '_view]').slideToggle("slow");
                });
                $(document).on('change', 'input[id*=pickDate_to_]', function () {
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
                $("input[id^='pickDate']").each(function () {
                    //$(this).datepicker("option", "maxDate", new Date($("#pickDate_to_" + (tabcounter - 1)).val()));

                });
                $(document).on('change', "select[id^='show_otherexpenses']", function () {
                    var id = $(this).attr('id').split("_");
                    $("div[id='" + id[1] + "_" + id[2] + "_" + id[3] + "']").fadeToggle('fast');
                });
                $(document).on('change', "select[id^='segment_expensestype']", function () {
                    var id = $(this).attr('id').split("_");
                    var item = $(this).find(':selected').attr('itemref');
                    $("div[id='Other_" + id[2] + "_" + id[3] + "']").hide();
                    if(item == 'Other') {
                        $("div[id='" + item + "_" + id[2] + "_" + id[3] + "']").show();
                    }

                });
                $(document).on('change', 'input[id^="numnight_segacc_"],input[id^="pricenight_segacc_"]', function () {
                    var id = $(this).attr("id").split("_");
                    if($('input[id="pricenight_' + id[1] + '_' + id[2] + '_' + id[3] + '"]').length < 0) {
                        return;
                    }
                    var name = $('input[id^="checksum_' + id[2] + '_' + id[3] + '_"][id$="_tmhid"]').attr("id").split("_");
                    $("div[id=total_" + id[1] + "_" + id[2] + '_' + id[3] + "]").fadeToggle('slow').stop().text($('input[id="pricenight_' + id[1] + '_' + id[2] + '_' + id[3] + '"]').val() * $('input[id="numnight_' + id[1] + '_' + id[2] + '_' + id[3] + '"]').val());
                    $('input[name="segment[' + name[2] + '][tmhid][' + name[3] + '_' + name[4] + '][subtotal]"]').val($('input[id="pricenight_' + id[1] + '_' + id[2] + '_' + id[3] + '"]').val() * $('input[id="numnight_' + id[1] + '_' + id[2] + '_' + id[3] + '"]').val());
                    $('input[name="segment[' + name[2] + '][tmhid][' + name[3] + '_' + name[4] + '][subtotal]"]').trigger('change');
                });
                $(document).on('click', 'input[id=finalize]', function () {
                    $('input[id="finalizeplan"]').val('1');
                    $('input[id="perform_travelmanager/plantrip_Button"]').click();
                    $('input[id="finalizeplan"]').val('');
                });
//on chagne from date refresh qnd trigger again
                $(document).on('change', 'input[id^="pickDate_to"]', function () {
                    var segid = $(this).attr("id").split("_");
                    var nextsegid = ++segid[2];
                    var descity = $('input[id="destinationcity_' + segid[2] + '_cache_id"]').val();
                    var origincity = $('input[id=cities_' + nextsegid + '_cache_id]').val(); /*get  the cityid from the hiiden field*/
                    $(document).on('change', 'input[id^="pickDate_from_' + nextsegid + '"]', function () {
                        //  $('input[id^="destinationcity_' + nextsegid + '"]').trigger('change');
                        sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=refreshtransp", "&sequence=" + nextsegid + "&destcity=" + descity + "&origincity=" + origincity + "&departuretime=" + $('#altpickDate_to_' + (nextsegid - 1)).val(), 'content_suggestedtransploader_' + nextsegid + '', 'content_suggestedtransp_' + nextsegid + '', true);
                    });
                    $('input[id^="pickDate_from_' + nextsegid + '"]').val($(this).val()) // set fromdate of the next segment to get the value of the previous ssegment
                    $('input[id^="altpickDate_from_' + nextsegid + '"]').val($(this).val());
                    $('input[id^="pickDate_from_' + nextsegid + '"]').trigger('change');
                    if((descity != '') && $("#altpickDate_to").val() != '') {
                        // $('input[id^="destinationcity_' + nextsegid + '"]').trigger('change');
                    }
                });
                $(document).on('click', 'input[id^="noAccomodation_"]', function () {
                    var id = $(this).attr("id").split("_");
                    if(!$($('#segment_hotels_' + id[1] + ', #other_hotels_' + id[1])).is(':checked'))) {
                        $('#segment_hotels_' + id[1] + ', #other_hotels_' + id[1]).find('input').val("")
                    }
                });
                if($('input[id^="specifyaffent_"]').is(':checked')) {
                    var id = $('input[id^="specifyaffent_"]').attr("id").split("_");
                    $('tr[id="specifyaffent_' + id[1] + '_block"]').show();
                }
                $(document).on('click', 'input[id^="specifyaffent_"]', function () {
                    var segvar = $(this).val();
                    var id = $(this).attr("id").split("_");
                    var segid = id[1];
                    if($(this).is(':checked')) {
                        $('tr[id="specifyaffent_' + segid + '_block"]').show();
                        $('input[id="allentities_' + segid + '_cache_id"]').removeAttr('disabled');
                        $('input[name="segment[' + segid + '][affid]"]').removeAttr('disabled');
                    }
                    else {
                        $('tr[id="specifyaffent_' + segid + '_block"]').hide();
                        $('input[id="allentities_' + segid + '_cache_id"]').attr('disabled', 'disabled');
                        $('input[name="segment[' + segid + '][affid]"]').attr('disabled', 'disabled');
                    }
                });
                $('input[id^=segment_1_tmtcid_][id$="fare"]').each(function (i, obj) {
                    populate_suggestions(obj);
                    return false;
                });
                $(document).on('change', 'input[name^="segment"][name$="[fare]"],input[name^="segment"][name$="[subtotal]"],select[name^="segment"][name$="[currency]"],input[name^="segment"][name$="[expectedAmt]"]', function () {
                    populate_suggestions(this);
                });
                $(document).on('click', 'button[id^="airflights_button_"]', function () {
                    var id = $(this).attr("id").split("_");
                    $('div[id="airflights_div_' + id[2] + '"]').fadeToggle();
                });
            });
            function populate_suggestions(obj) {
                var id = $(obj).attr("id").split("_");
                var total = {
                };
                $('input[id^="segment_' + id[1] + '_"][id$="_fare"]').each(function (i, obj) {
                    var fareid = $(obj).attr("name").slice(0, -6);
                    var farecur = $('select[name="' + fareid + '[currency]"] option:selected').text();
                    var price = $(obj).val();
                    if(price > 0) {
                        if(typeof total[farecur] == "undefined") {
                            total[farecur] = parseInt(price, 10);
                        }
                        else {
                            total[farecur] = total[farecur] + parseInt(price, 10);
                        }
                    }
                });
                $('input[name^="segment[' + id[1] + ']"][name$="[subtotal]"]').each(function (i, obj) {
                    var priceid = $(obj).attr("name").slice(0, -10);
                    var pricecur = $('select[name^="' + priceid + '[currency]"] option:selected').text();
                    var price = $(obj).val();
                    if(price > 0) {
                        if(typeof total[pricecur] == "undefined") {
                            total[pricecur] = parseInt(price, 10);
                        }
                        else {
                            total[pricecur] = total[pricecur] + parseInt(price, 10);
                        }
                    }
                });
                $('input[name^="segment[' + id[1] + ']"][name$="[expectedAmt]"]').each(function (i, obj) {
                    var expecid = $(obj).attr("name").slice(0, -13);
                    var expeccur = $('select[name="' + expecid + '[currency]"] option:selected').text();
                    var price = $(obj).val();
                    if(price > 0) {
                        if(typeof total[expeccur] == "undefined") {
                            total[expeccur] = parseInt(price, 10);
                        }
                        else {
                            total[expeccur] = total[expeccur] + parseInt(price, 10);
                        }
                    }
                });
                $(document).on('keyup', 'input[type="number"]', function () {
                    if(typeof $(this).attr('max') != typeof undefined) {
                        if($(this).attr('max').length > 0) {
                            if(parseInt($(this).val(), 10) > parseInt($(this).attr('max'), 10)) {
                                $(this).val($(this).attr('max'));
                            }
                        }
                    }
                });
                $(document).on('change', 'input[id^="purposes_checks_"]', function () {
                    var id = $(this).attr('id').split('_');
                    var empty = 1;
                    $('input[id="' + $(this).attr('id') + '"]').each(function (i, obj) {
                        if($(obj).is(":checked")) {
                            empty = 0;
                        }
                    });
                    if(empty == 1) {
                        $('[data-purposes="' + id[2] + '_' + id[3] + '"]').each(function (i, obj) {
                            $(obj).find('input').val('');
                            $(obj).find('select').val('');
                            $(obj).hide();
                        });
                    }
                    else {
                        $('[data-purposes="' + id[2] + '_' + id[3] + '"]').each(function (i, obj) {
                            $(obj).show();
                        })
                    }
                });
                var ptext = '';
                $.each(total, function (index, val) {
                    ptext += 'Total Amount In ' + index + ' is ' + val + '. ';
                });
                if(ptext.length > 0) {
                    $('p[id="finance_' + id[1] + '_suggestion"]').text(ptext);
                }
            }
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
        <td class="contentContainer" colspan="2">
            <h1>{$lang->plantrip}</h1>
            {$leave_ouput}
            <form name="perform_travelmanager/plantrip_Form" id="perform_travelmanager/plantrip_Form" action="#" method="post">
                <div style='margin-top: 10px; '>
                    <a id="createtab" class="showpopup" href="#"><img border="0" alt="{$lang->addsegment}" src="images/addnew.png"> {$lang->addsegment}</a>
                </div>
                <input type="hidden" value="{$sequence}" name="sequence"/>
                <input type="hidden" value="{$previoussegtodate}" id="todate" name="todate"/>
                <input type="hidden" value="{$previoussegdestcity}" id="prevdestcity" name="prevdestcity"/>
                <input type="hidden" value="{$leaveid}" id="lid" name="lid"/>
                <input type="hidden" value="{$planid}" id="lid" name="planid"/>
                {$plantript_segmentstabs}

                <input type="hidden" id="saveaddseg" name="saveaddseg" value="{$sequence}_saveaddseg">
                <input type='submit' style="cursor: pointer" class='button' value="{$lang->savecaps}" id='perform_travelmanager/plantrip_Button'>
                <input type="button"  style="cursor: pointer" class="button" value="{$lang->saveandopenseg}" id="save_addsegment"/>
                <a href="index.php?module=travelmanager/viewplan&id={$planid}&lid={$leaveid}&referrer=plan" target="_blank">
                    <input type="button" style="cursor: pointer" class='button' value="{$lang->preview}">
                </a>
                <input type="button" style="cursor: pointer" class='button'  value="{$lang->preview} & {$lang->finish}" id="finalize"/>
                <input type="hidden" value="" name="finalizeplan" id="finalizeplan"/>

            </form>

            <div id="perform_travelmanager/plantrip_Results"></div>
        </td>
    </tr>
    {$footer}
</body>
</html>