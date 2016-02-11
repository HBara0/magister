<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->requestleave}</title>
        {$headerinc}
        <script>
            $(function() {
                var tabs = $("#segmentstabs").tabs();
                var tabcounter = tabs.find(".ui-tabs-nav").find('li').length + 1; //find the  lenght of li tabs and increment by 1
                $(document).on('click', "#createtab", function() {
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
                        var templatecontent = sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=add_segment", "sequence=" + tabcounter + "&lid=" + $('#lid').val() + "&destcity=" + $('#destinationcity_' + (tabcounter - 1) + '_cache_id').val() + "&toDate=" + ($('#pickDate_to_' + (tabcounter - 1)).val()) + "&fromDate=" + ($('#pickDate_from_' + (tabcounter - 1)).val()) + "&leavetoDatetime=" + $('#leaveDate_to_' + (tabcounter - 1)).val() + "&toDate=" + $('#altpickDate_to_' + (tabcounter - 1)).val(), id, id, 'html', true); //'loadindsection'
                        var templatecontent = errormessage = '';
                        tabs.append("<div id=" + id + "><p>" + templatecontent + "</p></div>");
                        tabs.tabs("refresh");
                        $("#segmentstabs").tabs("option", "active", (tabcounter) - 1);
                        tabcounter = tabcounter + 1;
                    }

                });
                $(document).on('click', 'input[id="save_addsegment"]', function() {
                    if(timer) {
                        clearInterval(timer);
                        timer = null;
                    }
                    function click_seg() {
                        $('a[id="createtab"]').click();
                    }
                    $('input[id="saveaddseg"]').val("{$sequence}");
                    $('input[id="perform_travelmanager/plantrip_Button"]').click();
                    setTimeout(click_seg, 2000);
                    $('input[id="saveaddseg"]').val(0);
                });
                // close icon: removing the tab on click
                tabs.delegate("span.ui-icon-close", "click", function() {
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
                $(document).on('change', 'input[id^=destinationcity_],input[id^=pickDate_to_]', function() {
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
                        sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=populatecontent", "&sequence=" + sequence + "&destcity=" + ciid + "&origincity=" + origincity + "&departuretime=" + $('#altpickDate_from_' + sequence).val() + "&arrivaltime=" + $('#altpickDate_to_' + sequence).val() + "&parsetransp=1", 'content_detailsloader_' + sequence, 'content_details_' + sequence + '', true);
                        sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=populatecityprofile", "&sequence=" + sequence + "&destcity=" + ciid, 'segment_city_loader_' + sequence + '', 'segment_city_' + sequence + '', true);
                    }

                });
                $(document).on('click', 'input[id^=transp_lookuptransps_]', function() {
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }
                    var id = $(this).attr('id').split("_");
                    var sequence = id[2];
                    errormessage = '';
                    var ciid = $('input[id$=destinationcity_' + sequence + '_cache_id]').val(); /*get  the cityid from the hiiden field*/
                    if(typeof ciid !== typeof undefined && ciid !== '') {
                        var origincity = $('input[id=cities_' + sequence + '_cache_id]').val(); /*get  the cityid from the hiiden field*/
                        var transp = 0;
                        if($('input[id=transp_lookuptransps_' + sequence + ']:checked').val().length > 0) {
                            transp = $('input[id=transp_lookuptransps_' + sequence + ']:checked').val();
                        }

                        sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=refreshtransp", "&sequence=" + sequence + "&destcity=" + ciid + "&origincity=" + origincity + "&departuretime=" + $('#altpickDate_from_' + sequence).val() + "&arrivaltime=" + $('#altpickDate_to_' + sequence).val() + '&transp=' + transp + "&referrer=lookuptransps&othertranspdisplay=" + $("div[id^='show_othertransps_']").attr('style'), 'content_suggestedtransploader_' + sequence + '', 'content_suggestedtransp_' + sequence + '', true);
                        sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=populatecityprofile", "&sequence=" + sequence + "&destcity=" + ciid, 'segment_city_loader_' + sequence + '', 'segment_city_' + sequence + '', true);
                    }
                });
                /*var firstcategoryid = $('input[id*=transp_]').attr('id').split("_")[3];*/

                $(document).on('click', 'input[id*=transp_]', function() {
                    var id = $(this).attr('id').split("_");
                    var sequence = id[1];
                    var categoryid = id[3];
                    $('div[id=cat_content_' + categoryid + '_' + sequence + ']').slideToggle("slow");
                    /*ajax call to parse transpfields*/
                    //  sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=parsedetailstransp", "&categoryid=" + categoryid + "&sequence=" + sequence + "&catid=" + id[2], 'cat_detailsloader_' + categoryid + '', 'transpcat_content' + categoryid + '', true);

                });
                $(document).on('click', 'a[id^=countryhotels_][id$=_check]', function() {
                    var id = $(this).attr('id').split("_");
                    var sequence = id[1];
                    $('div[id=countryhotels_' + sequence + '_view]').slideToggle("slow");
                });
                $(document).on('change', 'input[id*=pickDate_to_]', function() {
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
                $(document).on('change', "select[id^='show_otherexpenses']", function() {
                    var id = $(this).attr('id').split("_");
                    $("div[id='" + id[1] + "_" + id[2] + "_" + id[3] + "']").fadeToggle('fast');
                });
                $(document).on('change', "select[id^='segment_expensestype']", function() {
                    var id = $(this).attr('id').split("_");
                    var item = $(this).find(':selected').attr('itemref');
                    $("div[id='Other_" + id[2] + "_" + id[3] + "']").hide();
                    if(item == 'Other') {
                        $("div[id='" + item + "_" + id[2] + "_" + id[3] + "']").show();
                    }
                });
                $(document).on("change", "input[id$='expamount']", function() {
                    var id = $(this).attr('id').split("_");
                    var from = new Date($("input[id='pickDate_from_" + id[1] + "']").val());
                    var to = new Date($("input[id='pickDate_to_" + id[1] + "']").val());
                    var timeDiff = Math.abs(to.getTime() - from.getTime());
                    var numnights = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1;
                    var expensetype = $("select[id='segment_expensestype_" + id[1] + "_" + id[2] + "']").val();
                    sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=validatefandbexpenses", "&amount=" + $("input[id='expenses" + "_" + id[1] + "_" + id[2] + "_expamount']").val() + "&numnights=" + numnights + "&currency=" + $("select[id='currency" + "_" + id[1] + "_" + id[2] + "_list']").val() + "&expensetype=" + expensetype, "fandb_warning_" + id[1] + "_" + id[2], "fandb_warning_" + id[1] + "_" + id[2], true);
                });
                $(document).on("change", "select[id^='currency'][id$='list']", function() {
                    var id = $(this).attr('id').split("_");
                    if(id[2] == 'accomodation') {
                        $("input[id^='pricenight_segacc_'][id$='" + id[1] + "']").trigger("change");
                    } else {
                        $("input[id$='" + id[2] + "_expamount']").trigger("change");
                    }
                });
                $(document).on('change', 'input[id^="numnight_segacc_"],input[id^="pricenight_segacc_"]', function() {
                    var id = $(this).attr("id").split("_");
                    if($('input[id="pricenight_' + id[1] + '_' + id[2] + '_' + id[3] + '"]').length < 0) {
                        return;
                    }
                    var name = $('input[id^="checksum_' + id[2] + '_' + id[3] + '_"][id$="_tmhid"]').attr("id").split("_");
                    $("div[id=total_" + id[1] + "_" + id[2] + '_' + id[3] + "]").fadeToggle('slow').stop().text($('input[id="pricenight_' + id[1] + '_' + id[2] + '_' + id[3] + '"]').val() * $('input[id="numnight_' + id[1] + '_' + id[2] + '_' + id[3] + '"]').val());
                    $('input[name="segment[' + name[2] + '][tmhid][' + name[3] + '_' + name[4] + '][subtotal]"]').val($('input[id="pricenight_' + id[1] + '_' + id[2] + '_' + id[3] + '"]').val() * $('input[id="numnight_' + id[1] + '_' + id[2] + '_' + id[3] + '"]').val());
                    $('input[name="segment[' + name[2] + '][tmhid][' + name[3] + '_' + name[4] + '][subtotal]"]').trigger('change');
                    var curr = $('select[name="segment[' + name[2] + '][tmhid][' + name[3] + '_' + name[4] + '][currency]"]').val();
                    var pricepernight = $('input[name="segment[' + name[2] + '][tmhid][' + name[3] + '_' + name[4] + '][priceNight]"]').val();
                    sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=checkpricevsavgprice", "&avgprice=" + $('input[id="avgprice_' + id[1] + '_' + id[2] + '_' + id[3] + '"]').val() + "&pricepernight=" + pricepernight + "&currency=" + curr, "hotelprice_warning_" + id[2] + "_" + id[3], "hotelprice_warning_" + id[2] + "_" + id[3], true);
                });
                $(document).on('click', 'input[id=finalize]', function() {
                    if(timer) {
                        clearInterval(timer);
                        timer = null;
                    }
                    $('input[id="finalizeplan"]').val('1');
                    $('input[id="perform_travelmanager/plantrip_Button"]').click();
                    $('input[id="finalizeplan"]').val('');
                });
                //on chagne from date refresh qnd trigger again
                $(document).on('change', 'input[id^="pickDate_to"]', function() {

                    var segid = $(this).attr("id").split("_");
                    var nextsegid = ++segid[2];
                    var descity = $('input[id="destinationcity_' + segid[2] + '_cache_id"]').val();
                    var origincity = $('input[id=cities_' + nextsegid + '_cache_id]').val(); /*get  the cityid from the hiiden field*/
                    $(document).on('change', 'input[id^="pickDate_from_' + nextsegid + '"]', function() {
                        //  $('input[id^="destinationcity_' + nextsegid + '"]').trigger('change');
                        sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=refreshtransp", "&sequence=" + nextsegid + "&destcity=" + descity + "&origincity=" + origincity + "&departuretime=" + $('#altpickDate_to_' + (nextsegid - 1)).val() + "&othertranspdisplay=" + $("div[id^='show_othertransps_']").attr('style') + "&othertranspsseccheckbox=" + othertranspsseccheckbox, 'content_suggestedtransploader_' + nextsegid + '', 'content_suggestedtransp_' + nextsegid + '', true);
                    });
                    $('input[id^="pickDate_from_' + nextsegid + '"]').val($(this).val()) // set fromdate of the next segment to get the value of the previous ssegment
                    $('input[id^="altpickDate_from_' + nextsegid + '"]').val($(this).val());
                    $('input[id^="pickDate_from_' + nextsegid + '"]').trigger('change');
                    if((descity != '') && $("#altpickDate_to").val() != '') {
                        // $('input[id^="destinationcity_' + nextsegid + '"]').trigger('change');
                    }
                });
                $(document).on('click', 'input[id^="noAccomodation_"]', function() {
                    var id = $(this).attr("id").split("_");
                    if($(this).is(':checked')) {
                        $('#segment_hotels_' + id[1] + ', #other_hotels_' + id[1]).find('input').val("");
                        $('#segment_hotels_' + id[1] + ', #other_hotels_' + id[1]).hide();
                    }
                    else {
                        $('#segment_hotels_' + id[1] + ', #other_hotels_' + id[1]).show();
                    }
                });
                if($('input[id^="specifyaffent_"]').is(':checked')) {
                    var id = $('input[id^="specifyaffent_"]').attr("id").split("_");
                    $('tr[id="specifyaffent_' + id[1] + '_block"]').show();
                }
                $(document).on('click', 'input[id^="specifyaffent_"]', function() {
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
                $('input[id^=segment_1_tmtcid_][id$="fare"]').each(function(i, obj) {
                    populate_suggestions(obj);
                    return false;
                });
                $(document).on('change', 'input[name^="segment"][name$="[fare]"],input[name^="segment"][name$="[subtotal]"],select[name^="segment"][name$="[currency]"],input[name^="segment"][name$="[expectedAmt]"]', function() {
                    var id = $(this).attr("id").split('_');
                    if(typeof id[2] != 'undefined') {
                        if(id[2] == 'tmpfid') {
                            return;
                        }
                    }
                    populate_suggestions(this);
                });
                $(document).on('click', 'button[id^="airflights_button_"]', function() {
                    var id = $(this).attr("id").split("_");
                    $('div[id="airflights_div_' + id[2] + '"]').fadeToggle();
                });
                var timer;
                $(document).on('click', "a[id^='save_section']", function() {
                    if(timer) {
                        clearInterval(timer);
                        timer = null;
                    }
                    var id = $(this).attr("id").split("_");
                    $("input[id^='save_section_" + id[2] + "_'][type='hidden']").val("0");
                    $("input[id='save_section_" + id[2] + "_" + id[3] + "_input'][type='hidden']").val("1");
                    $("input[id='perform_travelmanager/plantrip_Button']").trigger("click");
                    if(typeof id[3] != 'undefined') {
                        timer = setInterval(function() {
                            if($("div[id='sectionresults_" + id[3] + "']").html() != $("div[id='perform_travelmanager/plantrip_Results']").html()) {
                                $("div[id='sectionresults_" + id[3] + "']").html($("div[id='perform_travelmanager/plantrip_Results']").html());
                            }
                        }, 1000);
                    }
                });
                $(document).on('change', "select[id^='segment_tmtcid_'][id$='_othercategory']", function() {
                    var id = $(this).attr('id').split("_");
                    var value = $(this).find(':selected').val();
                    if(value == 1 || value == 2) {
                        $("div[id='segment_tmtcid_" + id[2] + "_" + id[3] + "_transpsextrafields']").show();
                    }
                    else {
                        $("div[id='segment_tmtcid_" + id[2] + "_" + id[3] + "_transpsextrafields']").hide();
                    }
                });

                $(document).on('change', "input[id^='allentities_'][id$='_cache_id_output']", function() {
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }
                    var value = $(this).val();
                    if(value != '0') {
                        var id = $(this).attr('id').split("_");
                        var dataParam = "id=" + value;
                        get = "suppliersegments";
                        loadingIn = "suppliersegments_" + id[1] + "_Loading";
                        contentIn = "supsegments_" + id[1];
                        var url = "index.php?module=travelmanager/plantrip&action=get_" + get;
                        $.ajax({
                            method: "post",
                            url: url,
                            data: dataParam,
                            beforeSend: function() {
                                $("#" + loadingIn).html("<img src='" + imagespath + "/loading.gif' alt='" + loading_text + "'/>")
                            },
                            complete: function() {
                                $("#" + loadingIn).empty();
                            },
                            success: function(returnedData) {
                                $("#" + contentIn).html(returnedData);
                            }

                        });
                    }
                });
            });
            var totalamountneeded = {
            };
            function populate_suggestions(obj) {
                var total = {
                };
                var id = $(obj).attr("id").split("_");
                $('input[id^="segment_' + id[1] + '_"][id$="_fare"]').each(function(i, obj) {
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
                var name = ($('input[name$="[flightNumber]"]:checked', "form[id='perform_travelmanager/plantrip_Form']").attr("name"));
                if(typeof name != 'undefined' && name != "") {
                    var name = name.split("[");
                    var fare = parseInt($("input[name$='[" + name[3] + "[" + name[4] + "[fare]']").val(), 10);
                    //segment[1][tmtcid][c25f31c9d6][1GkTOBumUgERGeVNn2WlkE001][currency]
                    var farecur = 'USD';
                }

                if(typeof fare !== 'undefined' && typeof fare != 'NaN') {
                    if(typeof total[farecur] == "undefined") {
                        total[farecur] = fare;
                    } else {
                        total[farecur] = total[farecur] + fare;
                    }
                }
                $('input[name^="segment[' + id[1] + ']"][name$="[subtotal]"]').each(function(i, obj) {
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
                $('input[name^="segment[' + id[1] + ']"][name$="[expectedAmt]"]').each(function(i, obj) {
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
                $(document).on('keyup', 'input[type="number"]', function() {
                    if(typeof $(this).attr('max') != typeof undefined) {
                        if($(this).attr('max').length > 0) {
                            if(parseInt($(this).val(), 10) > parseInt($(this).attr('max'), 10)) {
                                alert('maximum days exceeded');
                                $(this).val($(this).attr('max'));
                                $(this).trigger("change");
                            }
                        }
                    }
                });
                $(document).on('change', 'input[id^="purposes_checks_"]', function() {
                    var completeid = $(this).attr('id');
                    var id = $(this).attr('id').split('_');
                    var ltpid = $(this).val();
                    $("input[id='ajaxaddmoredata_ltpid_" + id[3] + "']").val(ltpid);
                    var purposeisevent = '';
                    var eventpurposeid = '';
                    var checked = '';
                    if(id[2] == 'external') {
                        checked = 0;
                        if($(this).is(":checked")) {
                            checked = 1;
                        }
                        $.post("index.php?module=travelmanager/plantrip&action=populateexternalpurpose", {externalpurposetype: ltpid, sequence: id[3]}, function(returnedData) {
                            var obj = JSON.parse(returnedData);
                            if(obj.event == 1) {
                                eventpurposeid = obj.eventpurposeid;
                                $("input[id='event']").val(1);
                                $("input[id='eventpurposeid']").val(eventpurposeid);
                                $("tr[id='events_" + id[3] + "_trow']").html(obj.htmlcontent);
                                purposeisevent = 1;
                                if(checked == 0) {
                                    $("tr[id='events_" + id[3] + "_trow']").hide();
                                } else {
                                    $("tr[id='events_" + id[3] + "_trow']").show();
                                }
                            } else {
                                $("input[id='event']").val(0);
                                purposeisevent = 0;
                            }
                        });
                    }
                    var t = setInterval(function() {
                        purposeisevent = String(purposeisevent);
                        if(purposeisevent.length > 0) {
                            if(t) {
                                clearInterval(t);
                                t = null;
                            }
                        }
                        if(purposeisevent == 0) {
                            var empty = 1;
                            var numchecked = 0;
                            $('input[id="' + completeid + '"]').each(function(i, obj) {
                                if($(obj).is(":checked")) {
                                    empty = 0;
                                    numchecked = numchecked + 1;
                                }
                            });
                            if(numchecked == 1 && $('input[id="' + completeid + '"][value="' + $("input[id='eventpurposeid']").val() + '"]').is(":checked")) {
                                empty = 1;
                            }
                            if(empty == 1) {
                                $('[data-purposes="' + id[2] + '_' + id[3] + '"]').each(function(i, obj) {
                                    $(obj).find('input').val('');
                                    $(obj).find('select').val('');
                                    $(obj).hide();
                                });
                            }
                            else {
                                $('[data-purposes="' + id[2] + '_' + id[3] + '"]').each(function(i, obj) {
                                    $(obj).show();
                                })
                            }
                        }

                    }, 1500);
                });
                var ptext = '';
                $.each(total, function(index, val) {
                    ptext += 'Total Amount In ' + index + ' is ' + val + '. ';
                });
                if(ptext.length > 0) {
                    $('p[id="finance_' + id[1] + '_suggestion"]').text(ptext);
                }
                totalamountneeded = total;
            }



            $(document).on('change', "input[id^='checkbox_show_othertransps_']", function() {
                var id = $(this).attr('id').split('_');
                if(this.checked) {
                    $("div[id^='show_othertransps_" + id[3] + "']").show();
                } else {
                    $("div[id^='show_othertransps_" + id[3] + "']").hide();
                }
            });
            $(document).on('change', "select[id^='segment'][id$='_class']", function() {
                var id = $(this).attr('id').split('_');
                sharedFunctions.requestAjax("post", "index.php?module=travelmanager/plantrip&action=validatetranspclass", "&transpclass=" + $(this).val(), "transpclass_warning_" + id[1] + "_" + id[3], "transpclass_warning_" + id[1] + "_" + id[3], true);
                var x = $("div[id='perform_travelmanager/plantrip_Results]").val();
            });
            $(document).on('focus', "input[id^='pickDate_to_'],input[id^='destinationcity_'][id$='cache_autocomplete']", function() {
                var id = $(this).attr("id").split("_");
                var segid = id[2];
                if(id[0] == 'destinationcity') {
                    segid = id[1];
                }
                $("p[id='pickDateto_warning_" + segid + "']").html('Please note that changing this field will refresh all the segment sections');
            });
            $(document).on('focusout', "input[id^='pickDate_to_'],input[id^='destinationcity_'][id$='cache_autocomplete']", function() {
                var id = $(this).attr("id").split("_");
                var segid = id[2];
                if(id[0] == 'destinationcity') {
                    segid = id[1];
                }
                $("p[id='pickDateto_warning_" + segid + "']").html('');
            });
            $(document).on('click', "input[id^='preview']", function() {
                var data = $("form").serialize();
                var previewlink = rootdir + $("input[id='preview_link']").val();
                $.ajax({type: 'post',
                url: rootdir +'index.php?module=travelmanager/plantrip&action=do_perform_plantrip',
                data: data,
                beforeSend: function() {},
                complete: function() {},
                success: function(returnedData) {
                    window.open(previewlink, '_blank');
            }
                });
            });
            $(document).on('change', "input[id^='segment'][id$='amount'],select[name^='segment'][name$='currency]']", function() {
                var totalamounts = '';
                $.each(totalamountneeded, function(index, val) {
                    $('input[id^="segment"][id$="amount"]').each(function(i, obj) {
                        var id = $(obj).attr("id").split("_");
                        var curramount = $(obj).val()
                        var currency = $('select[name="segment[' + id[1] + '][tmpfid][' + id[3] + '][currency]"] option:selected').text();
                        if(currency == index) {
                            if(curramount > val + ((val * 10) / 100)) {
                                $("td[id='segment_" + id[1] + "_tmpfid_" + id[3] + "_results']").html("You are asking in advance for 10% more than the planned amount in " + index);
                            } else {
                                $("td[id='segment_" + id[1] + "_tmpfid_" + id[3] + "_results']").html("");
                            }
                        }
                    });
                });
                var id = $(this).attr("id").split("_");
                if(id[4] == 'currency') {
                    var currency_matched = false;
                    $.each(totalamountneeded, function(index, val) {
                        var curr = $('select[name="segment[' + id[1] + '][tmpfid][' + id[3] + '][currency]"] option:selected').text();
                        if(curr == index) {
                            currency_matched = true;
                        }
                    });
                    if(currency_matched == false) {
                        $("td[id='segment_" + id[1] + "_tmpfid_" + id[3] + "_results']").html("");
                    }
                }
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
        <td class="contentContainer" colspan="2">
            <h1 id="tmsections-1">{$lang->plantrip}</h1>
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


                <!--  <a href="#" target="_blank" id="preview">-->
                <input type="hidden" value="index.php?module=travelmanager/viewplan&id={$planid}&lid={$leaveid}&referrer=plan" id="preview_link"/>
                <input type="button" id="preview" style="cursor: pointer" class='button' value="{$lang->preview}">
                <!-- </a>-->
                <input type="hidden" id="saveaddseg" name="saveaddseg" value="{$sequence}_saveaddseg">
                <input type="button"  style="cursor: pointer" class="button" value="{$lang->saveandopenseg}" id="save_addsegment"/>

                <input type='submit' style="cursor: pointer;display:none" class='button' value="{$lang->savecaps}" id='perform_travelmanager/plantrip_Button'>

                <input type="button" style="cursor: pointer" class='button'  value="{$lang->preview} & {$lang->finish}" id="finalize"/>
                <input type="hidden" value="" name="finalizeplan" id="finalizeplan"/>
                <div style='width:100%'>
                    <a href="#" style="float:right" title='{$lang->backtotop}'><img src="{$core->settings[rootdir]}/images/icons/backtotop.png"/></a>
                </div>
            </form>
            <div id="perform_travelmanager/plantrip_Results"></div>
        </td>
    </tr>
    {$footer}

    <!-- Start Tour -->
    {$helptour}

</body>
</html>