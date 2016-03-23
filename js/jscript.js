$(function () {
    /*
     Check the browswer support before anything else
     */


    //applyin the DATATABLES plugin on classes-START
    function initialize_datatables() {
        // Remove the formatting to get integer data for summation
        var intVal = function (i) {
            return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                    i : 0;
        };
        $(".datatable_basic").each(function (i, obj) {
            //basic grid type
            var maintable = obj;
            if($(maintable).hasClass('datatable_basic')) {
                //check if data attribute of totals columns exists and not empty, then fill the values
                if($(maintable).attr('data-totalcolumns')) {
                    var totalcolumns = $(maintable).attr('data-totalcolumns');
                }
                //create a second thead right after the firse one
                if($(maintable).attr('data-skipfilter') !== 'true') {
                    $(maintable).find('thead:first-child').after($(maintable).find('thead:first-child').clone());
                    // Setup - add a text input to each footer cell
                    $(maintable).find('thead:nth-child(2)').each(function (i, tfoot) {
                        $(tfoot).find('th').each(function (i, th) {
                            var title = $(th).text();
                            if(title.trim().length != 0) {
                                $(th).html('<input type="text" placeholder="Search ' + title + '" />');
                            }
                        });
                    });
                }
                var table = $(maintable).DataTable(
                        {
                            stateSave: true,
                            "pagingType": "full_numbers",
                            "initComplete": function () {
                                if($(maintable).attr('data-checkonclick') === 'true') {
                                    var api = this.api();
                                    api.$('td').click(function (e) {
                                        var chk = $(this).closest("tr").find("input:checkbox").get(0);
                                        if(e.target != chk)
                                        {
                                            chk.checked = !chk.checked;
                                        }
                                    });
                                }
                            },
                            "footerCallback": function (row, data, start, end, display) {
                                var api = this.api(), data;
                                if(typeof totalcolumns == 'undefined') {
                                    return;
                                }
                                var columns = totalcolumns.split(',');
                                if(!($.isArray(columns))) {
                                    return;
                                }
                                $.each(columns, function (i, col) {
                                    if(!($.isNumeric(intVal(col)))) {
                                        return true;
                                    }
                                    // Total over all pages
                                    total = api
                                            .column(col)
                                            .data()
                                            .reduce(function (a, b) {
                                                return intVal(a) + intVal(b);
                                            }, 0);

                                    // Total over this page
                                    pageTotal = api
                                            .column(col, {page: 'current'})
                                            .data()
                                            .reduce(function (a, b) {
                                                return intVal(a) + intVal(b);
                                            }, 0);

                                    // Update footer
                                    //if variables are not numeric skip and leave normal filters
                                    if(!($.isNumeric(pageTotal)) || !($.isNumeric(total))) {
                                        return;
                                    }
                                    else {
                                        $(api.column(col).footer()).html(
                                                pageTotal.toFixed(2) + ' <br>(Total: ' + total.toFixed(2) + ')'
                                                );
                                    }
                                });
                            }
                        });
                //apply filters on the second thead
                table.columns().every(function () {
                    var that = this;
                    $('input', $(maintable).find('thead:nth-child(2)').find('th').eq(this.index())).on('keyup change', function () {
                        if(that.search() !== this.value) {
                            that
                                    .search(this.value)
                                    .draw();
                        }
                    });
                });




                $(maintable).find('tbody').each(function (i, obj2) {
                    $(obj2).on('mouseenter', 'td', function () {
                        var colIdx = table.cell(this).index().column;
                        $(table.cells().nodes()).removeClass('highlight');
                        $(table.column(colIdx).nodes()).addClass('highlight');
                    });
                });
                $('.dataTables_filter').append('&nbsp;&nbsp;<img  title="Clear Filters" src="' + rootdir + '/images/icons/clearfilters.png" style="cursor:pointer;" id="datatables_cleafilters">');
            }

        });
    }
    initialize_datatables();
    $(document).on("click", 'img[id="datatables_cleafilters"]', function () {
        var clearfilters = function (obj) {
            var table = $(obj).DataTable();
            table.search('')
                    .columns().search('')
                    .draw();
        };
        var table = $(this).closest('.dataTables_wrapper').find('table:first');
        clearfilters(table);
    });

    //applyin the DATATABLES plugin on classes-END

    if(jQuery.support.leadingWhitespace == false) {
        $('head').append('<link rel="stylesheet" href="' + rootdir + 'css/jqueryuitheme/jquery-ui-current.custom.min.css" type="text/css" />');
        $("body").append("<div id='browserversionerror' title='Browser version is too old'>Please upgrade your browser to a newer version.</div>");
        $("#browserversionerror").dialog({
            bgiframe: true,
            closeOnEscape: false,
            modal: true,
            width: 460,
            maxWidth: 460,
            minHeight: 50,
            zIndex: 1,
            draggable: false
        });
    }

    $("input:checkbox[id$='_checkall']").click(function () {
        var id = $(this).attr('id').split("_");
        $('input:checkbox[id^="' + id[0] + '"]:visible').not(this).prop('checked', this.checked);
    });
    $('tr[class*="trowtools"]').hover(function () {
        $(this).toggleClass('altrow2').children('td [id$="_tools"]').find('div').toggle();
    });
    $(document).on("click", "#login_Button", login);
    $("#login_Form input").bind('keypress', function (e) {
        if(e.keyCode == 13) {
            login();
        }
    });
    $("#currentmodule_name").hover(function () {
        $(this).toggleClass("currentmodule_name_hover");
    });
    $("#currentmodule_name").click(function () {
        $(".moduleslist_container").fadeToggle('fast');
    });
    $("#mainmenu > li").hover(function () {
        $(this).not(":has(ul)").addClass("mainmenuitem_hover");
        if($(this).find("div").css("display") == "none") {
            $(this).addClass("mainmenuitem_hover");
        }
        else if($(this).find("div").css("display") != "none") {
            $(this).find("div > ul > li").hover(function () {
                $(this).toggleClass("submenuitem_hover");
            });
        }
    },
            function () {
                $(this).removeClass("mainmenuitem_hover");
            });
    $("#mainmenu > li[class^='expandable']").find("span:first").click(function() {
        $(this).parent().removeClass("mainmenuitem_hover");
        $("#" + $(this).attr("id") + "_children_container").slideToggle();
    });
    $("#mainmenu > li[class!='expandable']").not(":has(div)").find("span").click(function () {
        window.location = "index.php?module=" + $(this).attr("id");
    });
    $("#mainmenu > li > div > ul > li").find("span").click(function () {
        window.location = "index.php?module=" + $(this).attr("id");
    });
    if($(window).height() < 765) {
        $("#sidedesignImage").height($(window).height());
    }
    function destroy_texteditors(parent) {
        parent.find(".txteditadv,.inlinetxteditadv,.basictxteditadv").each(function () {
            var id = $(this).attr('id');
            try {
                if(CKEDITOR.instances[id]) {
                    CKEDITOR.instances[id].destroy();
                }
            }
            catch(e) {
                alert(e);
            }
        });
    }
    initialize_texteditors();
    function initialize_texteditors() {
        if($(".inlinetxteditadv,.txteditadv,.basictxteditadv,.htmltextedit").length > 0) {
            $(".inlinetxteditadv,.txteditadv,.basictxteditadv,.htmltextedit").each(function () {
                var id = $(this).attr('id');
                try {
                    if(CKEDITOR.instances[id]) {
                        CKEDITOR.instances[id].destroy();
                    }
                    if($(this).hasClass('inlinetxteditadv')) {
                        CKEDITOR.inline(id);
                        CKEDITOR.instances[id].config.removePlugins = 'horizontalrule,pagebreak,table,tabletools,colorbutton,find,flash,font,forms,iframe,image,newpage,removeformat,smiley,specialchar,stylescombo,templates';
                    }
                    else {
                        CKEDITOR.replace(id);
                        if($(this).hasClass('basictxteditadv')) {
                            CKEDITOR.instances[id].config.removePlugins = 'horizontalrule,pagebreak,table,tabletools,colorbutton,find,flash,font,forms,iframe,image,newpage,removeformat,smiley,specialchar,stylescombo,templates';
                        }
                        else if($(this).hasClass('htmltextedit')) {
                            CKEDITOR.instances[id].config.removePlugins = 'horizontalrule,pagebreak,table,tabletools,colorbutton,find,flash,font,forms,iframe,image,newpage,removeformat,smiley,specialchar,stylescombo,templates';
                            CKEDITOR.instances[id].config.startupMode = 'source';
                        }
                    }
                }
                catch(e) {
                    alert(e);
                }
            });
        }
    }
    if($(".texteditormin, .texteditor").length > 0) {
//if($('link[id="' + rootdir + 'js/redactor.min.js"') == 'undefined') {
        $('head').append('<script src="' + rootdir + 'js/redactor.min.js" type="text/javascript"></script>');
        $('head').append('<link rel="stylesheet" href="' + rootdir + 'css/redactor.css" type="text/css" />');
        if($(".texteditor").length > 0) {
            $('.texteditor').redactor({buttons: ['html', '|', 'formatting', '|', 'bold', 'italic', 'deleted', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'table', '|', 'alignment', '|', 'horizontalrule']});
        }

        if($(".texteditormin").length > 0) {
            $('.texteditormin').redactor({
                air: true,
                airButtons: ['bold', 'italic', 'deleted', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'alignleft', 'aligncenter', 'alignright', 'justify'],
                allowedTags: ["br", "p", "b", "i", "del", "strike", "blockquote", "cite", "small", "ul", "ol", "li", "dl", "dt", "dd", "sup", "sub", "pre", "strong", "em"]
            });
        }
//}
    }

    if($(".tablefilters_row_toggle").length > 0) {
        $(window).on('keydown', function (e) {
            if((e.which == 102 || e.which == 70) && e.ctrlKey) {
                e.preventDefault();
                $('#tablefilters, .tablefilters_row').toggle();
                $('html, body').animate({scrollTop: 0}, 'fast');
                $('#tablefilters').find('input:first').focus();
                return false;
            }
        });
    }
    //    $("input[id^='pickDate']").datepicker({maxDate: "+1d"});
//    $(this).datepicker("option", "maxDate", "+1d ");

    $("input[id^='pickDate']").each(function () {
        if(/^pickDate_/.test($(this).attr("id")) && /_to$/.test($(this).attr("id"))) {
            var id = $(this).attr("id").split('_');
            var secid = '';
            if(id.length > 2) {
                secid = id[0] + '_' + id[1] + '_from';
            }
            else {
                secid = id[0] + '_from';
            }
            $(this).datepicker({altField: "#alt" + $(this).attr('id'), altFormat: 'dd-mm-yy', dateFormat: 'MM dd, yy', showWeek: true, firstDay: 1, changeMonth: true, changeYear: true, showAnim: 'slideDown',
                onSelect: function (selectedDate) {
                    $("#" + $(this).attr("id") + "").trigger('change');
                    $("#" + secid + "").datepicker("option", "maxDate", selectedDate);
                }});
            $("#ui-datepicker-div").css("z-index", $(this).parents(".ui-dialog").css("z-index") + 1);
        }
        else if(/^pickDate_/.test($(this).attr("id")) && /_from$/.test($(this).attr("id"))) {
            var id = $(this).attr("id").split('_');
            var secid = '';
            if(id.length > 2) {
                secid = id[0] + '_' + id[1] + '_to';
            }
            else {
                secid = id[0] + '_to';
            }
            $(this).datepicker({altField: "#alt" + $(this).attr('id'), altFormat: 'dd-mm-yy', dateFormat: 'MM dd, yy', showWeek: true, firstDay: 1, changeMonth: true, changeYear: true, showAnim: 'slideDown',
                onSelect: function (selectedDate) {
                    $("#" + $(this).attr("id") + "").trigger('change');
                    $("#" + secid + "").datepicker("option", "minDate", selectedDate);
                }});
            $("#ui-datepicker-div").css("z-index", $(this).parents(".ui-dialog").css("z-index") + 1);
        } else {
            initalisedatepicker(this);
        }
    })

    function initalisedatepicker(object) {
        $(object).datepicker({altField: "#alt" + $(object).attr('id'), altFormat: 'dd-mm-yy', dateFormat: 'MM dd, yy', showWeek: true, firstDay: 1, changeMonth: true, changeYear: true, showAnim: 'slideDown'});
        $("#ui-datepicker-div").css("z-index", $(object).parents(".ui-dialog").css("z-index") + 1);
    }

    $(document).on("keyup", "input[class*='inlinefilterfield']", function () {
        var parentContainer = $(this).closest('table');
        setTimeout(function () {
            var totals = [];
            parentContainer.children('tbody').find('tr').each(function () {
                var toggle = 'show';
                $(this).show();
                $(this).find('td').each(function () {
                    var filterfield = parentContainer.children('thead').find('th:nth-child(' + ($(this).index() + 1) + ') > input[class*="inlinefilterfield"]');
                    if(filterfield.length == 0) {
                        return;
                    }

                    var text = $(this).text().toLowerCase();
                    var term = filterfield.val().toLowerCase();
                    if(term.length == 0) {
                        return;
                    }

                    if(text.indexOf(term) == -1) {
                        toggle = 'hide';
                        return false;
                    }
                });
                if(toggle == 'hide') {
                    $(this).hide();
                }
            });
            parentContainer.children('tbody').find('tr:visible').each(function () {
                $(this).find('td').each(function () {
                    var coltext = $(this).text().replace(/,/g, '');
                    if(!isNaN(coltext) && coltext.length != 0) {
                        if(isNaN(totals[$(this).index() + 1])) {
                            totals[$(this).index() + 1] = 0;
                        }
                        totals[$(this).index() + 1] += parseFloat(coltext);
                    }
                });
            });
            parentContainer.children('tfoot').find('tr').each(function () {
                $(this).find('td.coltotal,td.colavg').each(function () {
                    if(typeof totals[$(this).index() + 1] == 'undefined') {
                        return;
                    }
                    $(this).text(totals[$(this).index() + 1].toFixed(3));
                    if($(this).attr('class') == 'colavg') {
                        $(this).text((totals[$(this).index() + 1] / (parentContainer.children('tbody').find('tr:visible').length)).toFixed(3));
                    }
                });
            });
        }, 300);
    });
    $(document).on("click", "input[id^='pickDate']", function () {
        if(!$(this).hasClass('hasDatepicker')) {
            initalisedatepicker(this);
            $(this).focus();
        }
    });
    var accache = {};
    $(document).on("keyup", "input[id$='_autocomplete']", function () {
        if(sharedFunctions.checkSession() == false) {
            return;
        }
        var id = $(this).attr("id").split("_");
        var restrictcountry = $("input[id='restrictcountry']").val();
        var valueIn = '#' + $(this).attr("id").replace("_autocomplete", "_id");
        if($(this).val().length == 0) {
            $(valueIn).val("");
            $(valueIn + "_output").val("");
        }
        var filtersQuery = "";
        if($(this).attr('data-autocompletefilters')) {
            var attrs = $(this).attr('data-autocompletefilters').split(',');
            $.each(attrs, function (i) {
                attrs[i] = $.trim(attrs[i]);
                if($("input[id='" + attrs[i] + "']").length > 0) {
                    if($("input[id='" + attrs[i] + "']").val() != '') {
                        if($("input[id='" + attrs[i] + "']").attr('data-alternativename')) {
                            filtersQuery += "&" + $("input[id='" + attrs[i] + "']").attr('data-alternativename') + "=" + $("input[id='" + attrs[i] + "']").val();
                        }
                        else {
                            filtersQuery += "&" + attrs[i] + "=" + $("input[id='" + attrs[i] + "']").val();
                        }
                    }
                }
            });
        }
        $(this).autocomplete({
            source: function (request, response) {
                var term = request.term;
                if(id[id.length - 2] == 'cache') {
                    if(term in accache) {
                        response(accache[ term ]);
                        return;
                    }
                }

                var exclude = "";
                var comma = "";
                var count = 1;
                if(id[1] != 'noexception') {
                    var inputselection_extra = '';
                    if(id[1] == 'sectionexception') {
                        inputselection_extra = "[id*='" + id[2] + "']";
                    }

                    $("input[id^='" + id[0] + "']" + inputselection_extra + "[id$='_id']").each(function () {
                        if($(this).val().length > 0) {
                            exclude += comma + $(this).val();
                            if(++count != 1) {
                                comma = ",";
                                console.log(exclude)
                            }
                        }
                    });
                }

                var filters = new Array("rid", "spid", "cid", "spid[]", "coid", "countryid", "city", "hasMOM", "userlocation", "reserveFrom", "reserveTo");
                for(var i = 0; i < filters.length; i++) {
                    if($("input[name='" + filters[i] + "']").length > 0) {
                        if($("input[name='" + filters[i] + "']").val() != '') {
                            filtersQuery += "&" + filters[i] + "=" + $("input[name='" + filters[i] + "']").val();
                        }
                    }
                }

                if(filtersQuery.length > 0) {
                    filtersQuery += "&filter=1";
                }

                $.getJSON(rootdir + "search.php?type=quick&returnType=json&for=" + id[0] + "&exclude=" + exclude + filtersQuery, {
                    value: term
                }, function (data, status, xhr) {
                    if(id[id.length - 2] == 'cache') {
                        accache[ term ] = data;
                    }
                    response(data);
                });
            },
            minLength: 2,
            select: function (event, ui) {
                $(valueIn).val(ui.item.id);
                if($(valueIn + "_output").length > 0) {
                    $(valueIn + "_output").val(ui.item.id);
                    $(valueIn).trigger('change');
                    $(valueIn + "_output").trigger('change');
                }
            }
        }).data('uiAutocomplete')._renderItem = function (ul, item) {
            if(typeof item.desc != 'undefined') {
                if(typeof item.style != 'undefined') {
                    return $("<li " + item.style + ">").append("<a>" + item.value + "<br><small>" + item.desc + "</small></a>").appendTo(ul);
                }
                return $("<li>").append("<a>" + item.value + "<br><small>" + item.desc + "</small></a>").appendTo(ul);
            }
            return $("<li>").append("<a>" + item.value + "</a>").appendTo(ul);
        };
    });
    $(document).on("keyup", "input[id$='_QSearch']", QSearch);
    function QSearch() {
        if(sharedFunctions.checkSession() == false) {
            return;
        }

        var inputValue = $(this).val();
        var id = $(this).attr("id").split("_");
        var searchInput = $(this);
        if(id.length >= 5) {
            var resultsIn = "#searchQuickResults";
            for(var i = 0; i < (id.length - 1); i++) {
                resultsIn += '_' + id[i];
            }
        }
        else
        {
            var resultsIn = "#searchQuickResults_" + id[0] + "_" + id[id.length - 2];
        }


        if($(resultsIn).length <= 0) {
            resultsIn = "#searchQuickResults_" + id[id.length - 2];
        }

        if($("#" + id[0] + "_" + id[id.length - 3] + "_" + id[id.length - 2] + "_id").length > 0) {
            var valueIn = "#" + id[0] + "_" + id[id.length - 3] + "_" + id[id.length - 2] + "_id";
        }
        else
        {
            var valueIn = "#" + id[0] + "_" + id[id.length - 2] + "_id";
        }

        var exclude = "";
        var comma = "";
        var count = 1;
        if(id[1] != 'noexception') {
            var inputselection_extra = '';
            if(id[1] == 'sectionexception') {
                inputselection_extra = "[id*='" + id[2] + "']";
            }

            $("input[id^='" + id[0] + "']" + inputselection_extra + "[id$='_id']").each(function () {
                if($(this).val().length > 0) {
                    exclude += comma + $(this).val();
                    if(++count != 1) {
                        comma = ",";
                    }
                }
            });
        }
        //if(count == 2) { exclude = ""; }

        filtersQuery = "";
        var filters = new Array("rid", "spid", "cid", "spid[]");
        for(var i = 0; i < filters.length; i++) {
            if($("input[name='" + filters[i] + "']").length > 0) {
                if($("input[name='" + filters[i] + "']").val() != '') {
                    filtersQuery += "&" + filters[i] + "=" + $("input[name='" + filters[i] + "']").val();
                }
            }
        }

        if(filtersQuery.length > 0) {
            filtersQuery += "&filter=1";
        }
        /*
         var ridQuery = "";
         if($("#rid").length > 0) {
         ridQuery = "&rid=" + $("#rid").val();
         }*/

        if(inputValue.length == 0)
        {
            $(resultsIn).hide();
            $(valueIn).val("");
            $(valueIn + "_output").val("");
        }
        else
        {
            $.post(rootdir + "search.php?type=quick&for=" + id[0] + "&exclude=" + exclude + filtersQuery, {value: "" + inputValue + ""},
            function (returnedData) {
                if(returnedData.length > 0) {
                    $(resultsIn).html(returnedData);
                    $(resultsIn).slideDown();
                    $("#searchResultsList > li").click(function () {
                        searchInput.val($(this).text());
                        $(valueIn).val($(this).attr("id"));
                        $(valueIn + "_output").val($(this).attr("id")).trigger('change');
                        $(resultsIn).slideUp();
                    });
                    /*$("a[id^='addnew_']").click(function() {
                     var id = $(this).attr("id").split("_");
                     alert(id);
                     popUp(id[1], id[0] + "_" + id[2]);
                     });*/

                    $(document).not("input[id='" + id[0] + "_" + id[1] + "_QSearch']").click(function () {
                        $(resultsIn).slideUp().empty();
                    });
                }
            });
        }
        inputValue = "";
    }

    $(document).on("change", "input.ajaxcheckbox", function () {
        if(sharedFunctions.checkSession() == false) {
            return;
        }

        var id = $(this).attr("id").split("_");
        if($(this).is(':checked') == false) {
            $(this).val('0');
        }

        sharedFunctions.requestAjax("post", "index.php?module=" + id[id.length - 2], 'action=' + id[0] + '&value=' + $(this).val() + '&id=' + id[1], $(this).attr("id") + '_Result', $(this).attr("id") + '_Result');
    });
    $(document).on("click", "input[type='submit'][id$='_Button']", function () {
        var id = $(this).attr("id").split("_");
        var formid = '';
        for(var i in CKEDITOR.instances) {
            CKEDITOR.instances[i].updateElement();
        }
        for(var i = 0; i < id.length - 1; i++) {
            formid += id[i] + "_";
        }

        $("form[id='" + formid + "Form']:not([action]),form[id='" + formid + "Form'][action='#']").submit(function (e) {
            e.preventDefault();
        });
    });
    $(document).on("click", "input[id^='perform_'][id$='_Button'],input[id^='add_'][id$='_Button'],input[id^='change_'][id$='_Button']", function () {
        if(sharedFunctions.checkSession() == false) {
            return;
        }

        for(instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }

        var id = $(this).attr("id").split("_");
        var formid = '';
        for(var i = 0; i < id.length - 1; i++) {
            formid += id[i] + "_";
        }

        var formData = $("form[id='" + formid + "Form']").serialize();
        var details = id[id.length - 2].split("/");
        var url = "index.php?module=" + id[id.length - 2];
        if(!formData.match(/action=[A-Za-z0-9]+/)) {
            url += "&action=do_" + id[0] + "_" + details[1];
        }
        if(details[1] == 'marketintelligencereport') {
            $("table #filter_options").hide();
        }
        sharedFunctions.requestAjax("post", url, formData, formid + "Results", formid + "Results");
    });
    $(document).on("click", "a[id^='showmore_'][href^='#']", function () {
        var id = $(this).attr("id").split("_");
        $("#" + id[1] + "_" + id[2]).toggle();
    });
    $(document).on("click", "img[id^='addmore_']", function () {
        sharedFunctions.addmoreRows($(this));
    });
    $(document).on("keyup", "input[id='email'],input[accept='email']", validateEmailInline);
    //$("input[id='email'],input[accept='email']").change(validateEmailInline);

    function validateEmailInline() {
        //var action = $("form:has(input[id='email'])").attr("id").substring(0, ($("form:has(input[id='email'])").attr("id").length - 5));
        var formId = $(this).closest('form').attr("id");
        var action = formId.substring(0, formId.length - 5);
        var form = $("form[id='" + formId + "']");
        var result = $(this).attr("id");
        if($(this).val() != '') {
            if(validateEmail($(this).val())) {
                $("input[id='" + action + "_Button']").removeAttr('disabled');
                $("#" + result + "_Validation").html("<img src='" + imagespath + "valid.gif' />");
            }
            else
            {
                $("input[id='" + action + "_Button']").attr("disabled", "true");
                $("#" + result + "_Validation").html("<img src='" + imagespath + "invalid.gif' />");
            }
        }
        else
        {
            $("input[id='" + action + "_Button']").removeAttr('disabled');
            $("#" + result + "_Validation").html("");
        }
    }

    $("input[type='button'][id$='_swap']").click(function () {
        var id = $(this).attr("id").split("_");
        if($("#" + id[0] + "_last").val().length > 0) {
            $("#" + id[0]).val($("#" + id[0] + "_last").val());
        }
    });
    $(document).on("keydown", "input[accept='numeric']", function (e) {
        if(e.keyCode > 31 && (e.keyCode < 48 || (e.keyCode > 57 && (e.keyCode < 96 || e.keyCode > 105) && e.keyCode != 190 && e.keyCode != 110 && e.keyCode != 16 && e.keyCode != 17 && e.keyCode != 59))) {
            //$(this).val($(this).val().substring(0, ($(this).val().length - 1)));
            e.preventDefault();
            return false
        }
        return true
    });
    $(document).on('click', "a[id='resetpassword']", function () {
        $("#logincontent").hide();
        $("#resetpasswordcontent").show();
    });
    $("#resetpassword_Button").click(function () {
        sharedFunctions.requestAjax("post", "users.php?action=reset_password", "email=" + $("input[id='email']").val(), "resetpassword_Results", "resetpassword_Results");
    });
    $("#changepassword_Button").click(function () {
        sharedFunctions.requestAjax("post", "users.php?action=do_changepassword", $("form[id='changepassword_Form']").serialize(), "changepassword_Results", "changepassword_Results");
    });
    $("#modifyprofile_Button").click(function () {
        var formData = $("form[id='modifyprofile_Form']").serialize();
        sharedFunctions.requestAjax("post", "users.php?action=do_modifyprofile", formData, "modifyprofile_Results", "modifyprofile_Results");
    });
    $("input[id='getReports']").click(function () {
        if(sharedFunctions.checkSession() == false) {
            return;
        }
        $.post("index.php?module=reporting/createreports&action=get_reports", {quarter: $("#quarter").val(), year: $("#year").val()},
        function (returnedData) {
            $("select[id='reports']").empty();
            $("select[id='reports']").html(returnedData);
        }
        );
    });
    function show_loginbox() {
        $('head').append('<link rel="stylesheet" href="' + rootdir + 'css/jqueryuitheme/jquery-ui-current.custom.min.css" type="text/css" />');
        popUp("", "popup_loginbox", "users.php");
    }

    function login() {
        $.post(rootdir + "users.php?action=do_login",
                {username: $("#username").val(), password: $("#password").val(), token: $("#logintoken").val()},
        function (returnedData) {
            if($("status", returnedData).text() == 'true') {
                var spanClass = 'green_text';
            } else {
                var spanClass = 'red_text';
            }

            $("#login_Results").html("<span class='" + spanClass + "'>" + $("message", returnedData).text() + "</span>").show();
            if($("#noredirect").val() != '1') {
                if($("status", returnedData).text() == "true") {
                    goToURL($("#referer").val());
                }
            }
        },
                'xml'
                );
    }

    $(document).on('click', "a[id$='_loadpopupbyid'],a[id^='mergeanddelete_'][id$='_icon'],a[id^='revokeleave_'][id$='_icon'],a[id^='approveleave_'][id$='_icon']", function () {
        var id = $(this).attr("id").split("_");
        //        var rel = $(this).prop("rel");
        //        var underscore = '_';
//        if(rel != '' || rel != null) {
//            id[1] = rel;
        //        }

        if(typeof $(this).attr("data-template") != 'undefined') {
            id[0] = $(this).attr("data-template");
        }

        if(typeof $(this).attr("data-id") != 'undefined') {
            id[1] = $(this).attr("data-id");
        }
        if(typeof $(this).attr("data-module") != 'undefined') {
            id[2] = $(this).attr("data-module");
        }
        popUp(id[2], id[0], id[1], $(this));
    });
    $(".showpopup,input[id^='showpopup_']").on("click", function () {
        var id = $(this).attr("id").split("_");
        $('#popup_' + id[1]).dialog('open');
        /* Make the parent dialog overflow as visible to completely display the  customer inline search results */
        $(".ui-dialog, #popup_" + id[1]).css("overflow", "visible");
    });
    if($("div[id^='popup_']").length > 0) {
        $("div[id^='popup_']").dialog({
            autoOpen: false,
            bgiframe: true,
            closeOnEscape: true,
            modal: true,
            width: 600,
            minWidth: 600,
            maxWidth: 800,
            close: function () {
                $(this).find("form").each(function () {
                    this.reset();
                });
                $(this).find("span[id$='_Validation']").empty();
                $(this).find("span[id$='_Results']").empty();
            }
        });
    }

    if($("div[id^='tabs_']").length > 0) {
        $("div[id^='tabs_']").tabs();
    }

    $(document).on("click", "a[id^='addnew_']", function () {
        var id = $(this).attr("id").split("_");
        popUp(id[1], id[0] + "_" + id[2]);
    });
    $('input[title],a[title],div[title],span[title]').qtip({style: {classes: 'ui-tooltip-green ui-tooltip-shadow'}, show: {event: 'focus mouseenter', solo: true}, hide: 'unfocus mouseleave', position: {viewport: $(window)}});
    function popUp(module, template, id, element) {
        if(element === undefined) {
            element = '';
        }
        if(id === undefined) {
            id = '';
        }

        if(id.length > 1) {
            id = id.split("_");
            var underscore = '';
            var uniquename = '';
        }
        else {
            uniquename = id;
        }

        for(i = 0; i < id.length; i++) {
            if(id[i].length > 1) {
                uniquename = uniquename + underscore + id[i];
                underscore = "_";
            }
        }
        id = uniquename;
        if(id != 'users.php') {
            if(sharedFunctions.checkSession() == false) {
                return;
            }
        }

        if(id === undefined) {
            id = '';
        }
        //$("#popupBox").hide("fast");

        //$(".contentContainer").append("<div id='popupBox'></div>");
        //if(jQuery.browser.msie) {
        //	$("#popupBox").css("top", ($(document).height()/2) + ($("#messageBox").offset().top/2));
        //$("#popupBox").css("position", "absolute");
        //}]


        if($("div[id^='popup_']").length > 0) {
            //$("div[id^='popup_']").remove();
        }

        var file = "index.php";
        if(module == '' && id != '') {
            file = rootdir + id;
        }
        var data_params = '';
        if(!jQuery.isEmptyObject(element)) {
            if(typeof element.attr("data-params") != 'undefined') {
                data_params = '&params=' + element.attr("data-params");
            }
        }
        /*change ajax call*/
        $.ajax({type: 'post',
            url: file + "?module=" + module + "&action=get_" + template,
            data: "id=" + id + data_params,
            beforeSend: function () {
                $("body").append("<div id='modal-loading'><span  style='display:block; width:100px; height: 100%; margin: 0 auto;'><img  src='./images/loader.gif'/></span></div>");
                $("#modal-loading").dialog({height: 150, modal: true, closeOnEscape: false, title: 'Loading...', resizable: false, minHeight: 0,
                });
            },
            complete: function () {
                $("#modal-loading").dialog("close").remove();
            },
            success: function (returnedData) {
                $(".container").append(returnedData);
                initialize_texteditors();
                $("div[id^='popup_']").dialog({
                    bgiframe: true,
                    closeOnEscape: true,
                    modal: true,
                    width: 600,
                    minWidth: 600,
                    maxWidth: 800,
                    zIndex: 1000,
                    close: function () {
                        destroy_texteditors($(this));
                        $(this).find("form").each(function () {
                            this.reset();
                        });
                        $(this).find("span[id$='_Validation']").empty();
                        $(this).find("span[id$='_Results']").empty();
                        $(this).remove();
                    }
                });
                /* Make the parent dialog overflow as visible to completely display the  customer inline search results */
                $(".ui-dialog,div[id^='popup_']").css("overflow", "visible");                 //$("#popupBox").html(returnedData).show("slow");
                //$("#popupBox").draggable();
                //	$("input[id$='_QSearch']").keyup(QSearch);
                //$("input[id='email']").keyup(validateEmailInline);
                $("input[id='email']").change(validateEmailInline);
                /*$("input[id$='_Button']").click(function() {
                 if($.cookie(cookie_prefix + 'uid') == null) {
                 window.location = window.location;
                 }
                 var id =  $(this).attr("id").split("_");

                 var formid = '';
                 for(var i=0;i<id.length-1;i++) {
                 formid += id[i]+ "_";
                 }

                 var formData = $("form[id='" + formid +"Form']").serialize();
                 var url = "index.php?module=" + id[id.length-2];

                 if(!formData.match(/action=[A-Za-z0-9]+/)) {
                 url += "&action=save_" + id[1];
                 }

                 sharedFunctions.requestAjax("post", url, formData, formid + "Results", formid + "Results");
                 });	*/
                $("input[id='hide_popupBox']").click(function () {
                    $("#popupBox").hide("fast");
                });
            }

        });
        // $.post(file,
        // {module: module, action: "get_" + template, id: id},

        //);
    }

    $("a[href='#'][id^='approve_']").click(function () {
        if(sharedFunctions.checkSession() == false) {
            return;
        }
        var details = $(this).attr("id").split("_");
        var data = "&action=do_" + details[0] + "&attribute=" + details[2] + "&newvalue=" + details[3] + "&id=" + details[4];
        sharedFunctions.requestAjax("post", "index.php?module=" + details[1], data, $(this).attr("id"), $(this).attr("id"));
    });
    $("input[class='inlineCheck']").blur(function () {
        if($(this).val().length != 0) {
            var parentId = $(this).parents("form").attr("id").split("_");
            var results = $(this).attr("id") + "_inlineCheckResult";
            var data = "&action=inlineCheck&attr=" + $(this).attr("name") + "&value=" + $(this).val();
            sharedFunctions.requestAjax("post", "index.php?module=" + parentId[1], data, results, results);
        }
    });
    $(document).on("click", "img[id^='ajaxaddmore_']", function () {
        sharedFunctions.ajaxAddMore($(this));
    });
    window.sharedFunctions = function () {
        function ajaxAddMore(object, callback) {
            if(sharedFunctions.checkSession() == false) {
                return;
            }

            var id = object.attr('id').split('_');
            var num_rows = 0;
            var uniquename = '';
            var underscore = '';
            for(i = 2; i < id.length; i++) {
                uniquename = uniquename + underscore + id[i];
                underscore = "_";
            }
            if($("#numrows_" + uniquename).length != 0) {
                var num_rows = parseInt($("#numrows_" + uniquename).val());
            }
            var url = rootdir + "index.php?module=" + id[1] + "&action=ajaxaddmore_" + id[2];
            if($("#moduletype_" + uniquename).length != 0) {
                var module = $("#moduletype_" + uniquename).val();
                var url = rootdir + module + "/" + "index.php?module=" + id[1] + "&action=ajaxaddmore_" + id[2];
                //       /* Make unique code for dialog */
                //  var date = new Date();
                //   var msecond = date.getMilliseconds();
            }

            $.ajax({type: 'post',
                url: url,
                data: "value=" + num_rows + "&id=" + id[id.length - 1] + "&" + object.parent().find($('input[id^=ajaxaddmoredata_]')).serialize(),
                beforeSend: function () {
                    //        if(id[1] == 'aro/managearodouments') {
                    //           $("body").append("<div id='modal-loading'>Please wait untill the calculation is done.</div>");
                    //            $("#modal-loading").dialog({height: 0, modal: true, closeOnEscape: false, title: 'Loading...', resizable: false, minHeight: 0,
                    //               open: function(event, ui) {
                    //                   $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
                    //               }
                    //            });
                    //            $("#modal-loading").attr('style', 'opacity:1; z-index:1000;height:100px;width:1000px');
                    //       } else {                     $("body").append("<div id='modal-loading'></div>");
                    $("#modal-loading").dialog({height: 0, modal: true, closeOnEscape: false, title: 'Loading...', resizable: false, minHeight: 0});
                    //  }
                },
                complete: function () {
                    //+msecond
                    $("#modal-loading").dialog("close").remove();
                }, success: function (returnedData) {
                    $('#' + uniquename + '_tbody').append(returnedData);
                    if($("#numrows_" + uniquename).length != 0) {
                        $("#numrows_" + uniquename).val(num_rows + 1);
                    }
                    /*find the offset of the first input in the last tr*/
                    if($('#' + uniquename + '_tbody > tr:last').find("input").filter(':visible:first').length) {
                        if(id[1] != 'aro/managearodouments') {
                            $("html, body").animate({scrollTop: $('#' + uniquename + '_tbody > tr:last').find("input").filter(':visible:first').offset().top}, 1000);
                        }
                        if(typeof (callback) === 'function') {
                            callback();
                        }
                    }
                }
            });
        }

        //  window.sharedFunctions = function() {
        function requestAjax(methodParam, urlParam, dataParam, loadingId, contentId, datatype, options) {
            //var datatype = 'html';
            /* Check if value = 1 just to ensure background compatibility with previous code */
            if(datatype == 1) {
                datatype = 'html';
            }
            if(typeof datatype == "undefined") {
                datatype = 'xml'
            }

            var image_name = 'loading-bar.gif';
            $.ajax({type: methodParam,
                url: urlParam, data: dataParam,
                beforeSend: function () {
                    $("div[id='" + loadingId + "'],span[id='" + loadingId + "']").html("<img style='padding: 5px;' src='" + imagespath + "/" + image_name + "'' alt='" + loading_text + "' border='0' />");
                },
                complete: function () {
                    if(loadingId != contentId) {
                        $("#" + loadingId).empty();
                    }
                },
                success: function (returnedData) {
                    if(datatype == 'xml') {
                        if($(returnedData).find('status').text() == 'true') {
                            var spanClass = 'green_text';
                        } else if($(returnedData).find('status').text() == 'false') {
                            var spanClass = 'red_text';
                        }
                        if($(returnedData).find('message').text().length > 0) {
                            $("div[id='" + contentId + "'],a[id='" + contentId + "'],span[id='" + contentId + "']").html("<span class='" + spanClass + "'><img src='" + imagespath + "/" + $(returnedData).find('status').text() + ".gif' border='0' alt=''/>&nbsp;" + $(returnedData).find('message').text() + "</span>");
                        }
                        else {
                            $("div[id='" + contentId + "'],a[id='" + contentId + "'],span[id='" + contentId + "']").html(returnedData).dialog();
                        }
                    }
                    else
                    {
                        $("#" + contentId).html($.trim(returnedData));
                        if(options != "undefined") {
                            if(options == 'animate') {
                                $("#" + contentId).slideDown("slow");
                            }
                        }
                    }
                }//,
                // dataType: datatype
            });
        }
        function checkSession() {
            if($.cookie(cookie_prefix + 'uid') == null || $.cookie(cookie_prefix + 'uid') == 0) {
                show_loginbox();
                return false;
            }
        }
        function addmoreRows(trigger) {
            if(typeof trigger == 'object') {
                var id = trigger.attr("id").split("_");
            }
            else
            {
                var id = trigger.split("_");
            }

            if($("#" + id[1] + "_" + id[2] + "_tbody").length > 0) {
                id[1] = id[1] + "_" + id[2];
            }

            var last = $("#" + id[1] + "_tbody > tr:last").attr("id");
            var increment = parseInt(last) + 1;
            //var template =  $("#"+ id[1] +"_tbody > tr:last").html();
            /*	if($.browser.msie) {
             alert(template);
             alert(increment);
             template = template.replace(/name=([a-z]+)_[\d]+_([a-z_]+)/gi, "name='$1_" + increment + "_$2'");
             template = template.replace(/name=([a-z]+)\[[\d]+\](\[[a-z_]+\])/gi, "name='$1[" + increment + "]$2'");
             template = template.replace(/name=([A-Za-z]+)_[\d]+/gi, "name='$1_" + increment +"'");
             template = template.replace(/id=([a-z]+)_[\d]+_([a-z_]+)/gi, "id='$1_" + increment + "_$2'");
             template = template.replace(/id=([a-z]+)\[[\d]+\](\[[a-z_]+\])/gi, "id='$1[" + increment + "]$2'");
             template = template.replace(/id=([A-Za-z]+)_[\d]+/gi, "id='$1_" + increment +"'");
             alert(template);
             //template = template.replace(/id=([a-z_0-9]+)_id[\d]+_([a-z_]+)/gi, "id='$1_id" + increment + "_$2'");
             //template = template.replace(/id=([a-z_0-9]+)_id[\d]+/gi, "id='$1_id" + increment + "'");
             }
             */
            //$("#"+ id[1] +"_tbody").append("<tr id='" + increment + "'>" + template + "</tr>");

            $("#" + id[1] + "_tbody > tr:last").clone(true).removeAttr('id').attr('id', increment).appendTo("#" + id[1] + "_tbody");
            /*if(!$.browser.msie) {
             $("#"+ id[1] +"_tbody > tr[id='" + increment + "']").find("input[name],select[name],div[name],textarea[name],img").each(function() {
             $(this).attr("name", $(this).attr("name").replace(last, increment.toString()));              });
             }*/
            var needed_attributes = ["id", "name"];
            $("#" + id[1] + "_tbody > div").scrollTop();
            $.each(needed_attributes, function (key, val) {
                //$("#"+ id[1] +"_tbody > tr[id='" + increment + "']").find("input,select,div[id],span,textarea[name],img[id],tbody").each(function() {

                $("#" + id[1] + "_tbody > tr[id='" + increment + "']").find("tr[" + val + "],input[" + val + "],select[" + val + "],div[" + val + "],span[" + val + "],textarea[" + val + "],img[" + val + "],tbody[" + val + "]").each(function () {
                    if($(this).attr(val).length == 0) {
                        return true;
                    }

                    if($(this).attr(val).search(/([A-Za-z_0-9]+)_[\d]+_([A-Za-z_]+)/gi) != -1) {
                        $(this).attr(val, $(this).attr(val).replace(/([A-Za-z_0-9]+)_[\d]+_([A-Za-z_]+)/gi, "$1_" + increment + "_$2"));
                    }
                    else if($(this).attr(val).search(/([A-Za-z_0-9]+)_id[\d]+_([A-Za-z_]+)/gi) != -1) {
                        $(this).attr(val, $(this).attr(val).replace(/([A-Za-z_0-9]+)_id[\d]+_([a-z_]+)/gi, "$1_id" + increment + "_$2"));
                    }
                    else if($(this).attr(val).search(/([A-Za-z_0-9]+)_[\d]/gi) != -1) {
                        $(this).attr(val, $(this).attr(val).replace(/([A-Za-z_0-9]+)_[\d]+/gi, "$1_" + increment));
                    }
                    else if($(this).attr(val).search(/([A-Za-z_0-9]+)_id[\d]/gi) != -1) {
                        $(this).attr(val, $(this).attr(val).replace(/([A-Za-z_0-9]+)_id[\d]+/gi, "$1_id" + increment));
                    }
                    else
                    {
                        $(this).attr(val, $(this).attr(val).replace(last, increment.toString()));
                    }

                    if($(this).attr("type") != 'checkbox' && $(this).attr("type") != 'radio') {
                        $(this).val("");
                    }
                    if($(this).attr("type") == 'checkbox') {
                        $(this).removeAttr('checked')
                    }

                    if($(this).is("span")) {
                        $(this).html("");
                    }


                    if($(this).hasClass('hasDatepicker')) {
                        $(this).removeClass('hasDatepicker');
                    }
                });
            });
            if(id[1] == 'keycustomers') {
                $("#" + id[1] + "_tbody > tr:last").find("td:first").html($("#" + id[1] + "_tbody tr:last").find("td:first").html().replace(last, increment.toString()));
            }
            if(id[1] == 'productsactivity') {
                $("#" + id[1] + "_tbody > tr[id='" + increment + "']").find("input").each(function () {
                    if($(this).attr("readonly") !== typeof undefined) {
                        if($(this).attr("readonly") == 'readonly') {
                            $(this).attr('readonly', false);
                        }
                    }
                });
            }

            //	$("input[id$='_QSearch']").keyup(QSearch);
            //$("input[id='email']").keyup(validateEmailInline);
            $("input[id='email']").change(validateEmailInline);
            if($("input[id='" + id[1] + "_numrows']").length > 0) {
                $("input[id='" + id[1] + "_numrows']").val(increment);
            }
            else
            {
                $("#numrows").val(increment);
            }
        }
        function sharedPopUp(module, template, id) {
            popUp(module, template, id);
        }
        function populateForm(formname, querystring, callback) {
            var json = null;
            $.ajax({type: 'post',
                dataType: "text",
                url: querystring,
                //  data: $(this).serialize() + "&" + $.param(data),

                complete: function () {
                    $("#modal-loading").dialog("close").remove();
                },
                success: function (returnedData) {
                    try {
                        if(!returnedData) {
                            //   $("#orderreference").val(''); //hardcoded temp
                        }
                        if(typeof returnedData !== typeof undefined && returnedData !== '') {
                            json = eval("(" + returnedData + ");"); /* convert the json to object */
                            var form = document.forms[formname];
                            $(form).populate(json, {resetForm: 0});
                        }

                        if(typeof (callback) === 'function') {
                            callback(json);
                        }
                    } catch(e) {
                        alert(returnedData);
                    }
                }
            });
        }
        return {
            "requestAjax": requestAjax, "checkSession": checkSession,
            "addmoreRows": addmoreRows,
            "sharedPopUp": sharedPopUp,
            "populateForm": populateForm,
            "ajaxAddMore": ajaxAddMore,
        }
    }();
    $("#dimensionfrom, #dimensionto").sortable({
        connectWith: ".sortable",
        revert: true, //revert to their new positions using a smooth animation.
        cursor: "wait",
        tolerance: "intersect", //overlaps the item being moved to the other item by 50%.
        placeholder: "ui-state-highlight",
        over: function () {
            $('.sortable-placeholder').hide();
        },
        dropOnEmpty: true, //Prevent all items in a list from being dropped into a separate, empty list
        start: function () {        /*  return back the Color of the  element to its origin Upon remove of the item */
            $("#dimensionto li").animate({
                opacity: 2.35,
                backgroundColor: "#cccccc",
            });
        },
        stop: function (event, ui) {
            $("#dimensionto li").css('background', '#92d050');
            $('#dimensions').val($("#dimensionto").sortable('toArray'));
        }});
    $(document).on('change', "[data-reqparent^='children-']", function () {
        var children = $(this).attr('data-reqparent').split('-');
        if(children.length > 1) {
            if($(this).attr('type') == 'checkbox') {
                if($(this).is(':checked')) {
                    for(i = 1; i < children.length; i++) {
                        $('#' + children[i] + '').attr("required", true);
                    }
                }
                else {
                    for(i = 1; i < children.length; i++) {
                        $('#' + children[i] + '').attr("required", false);
                    }
                }
            }
            else {
                if($(this).val().length < 1) {
                    for(i = 1; i < children.length; i++) {
                        $('#' + children[i] + '').attr("required", false);
                    }
                }
                else {
                    for(i = 1; i < children.length; i++) {
                        $('#' + children[i] + '').attr("required", true);
                    }
                }
            }
        }
    });
    // Toggle filter options when generating dimensional report
    $("a[id='filterby']").click(function () {
        $("table #filter_options").toggle();
    });
//}


});
function validateEmail(email) {
    return email.match(/^[a-zA-Z0-9&*+\-_.{}~^\?=\/]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9.-]+$/);
}

function IsNumeric(object, callFunction) {
    if(isNaN(parseFloat(object.value))) {
        alert("The number you have entered is not valid.");
        object.value = '';
        object.focus();
        return false;
    }
    if(callFunction != "") {
        setTimeout(callFunction, 0);
    }
    return true
}

function goToURL(url)
{
    if(url != '')
    {
        if(!window.location)
        {
            document.location = url;
        }
        else
        {
            window.location = url;
        }
    }
}


