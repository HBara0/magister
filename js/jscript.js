$(function() {
    /*
     Check the browswer support before anything else
     */
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

    $('tr[class*="trowtools"]').hover(function() {
        $(this).toggleClass('altrow2').children('td [id$="_tools"]').find('div').toggle();
    });

    $("#login_Button").live("click", login);
    $("#login_Form input").bind('keypress', function(e) {
        if(e.keyCode == 13) {
            login();
        }
    });

    $("#currentmodule_name").hover(function() {
        $(this).toggleClass("currentmodule_name_hover");
    });

    $("#currentmodule_name").click(function() {
        $(".moduleslist_container").fadeToggle('fast');
    });

    $("#mainmenu > li").hover(function() {
        $(this).not(":has(ul)").addClass("mainmenuitem_hover");

        if($(this).find("div").css("display") == "none") {
            $(this).addClass("mainmenuitem_hover");
        }
        else if($(this).find("div").css("display") != "none") {
            $(this).find("div > ul > li").hover(function() {
                $(this).toggleClass("submenuitem_hover");
            });
        }
    },
            function() {
                $(this).removeClass("mainmenuitem_hover");
            });

    $("#mainmenu > li[class='expandable']").find("span:first").click(function() {
        $(this).parent().removeClass("mainmenuitem_hover");
        $("#" + $(this).attr("id") + "_children_container").slideToggle();
    });

    $("#mainmenu > li[class!='expandable']").not(":has(div)").find("span").click(function() {
        window.location = "index.php?module=" + $(this).attr("id");
    });

    $("#mainmenu > li > div > ul > li").find("span").click(function() {
        window.location = "index.php?module=" + $(this).attr("id");
    });

    if($(window).height() < 765) {
        $("#sidedesignImage").height($(window).height());
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
        $(window).on('keydown', function(e) {
            if((e.which == 102 || e.which == 70) && e.ctrlKey) {
                e.preventDefault();
                $('#tablefilters, .tablefilters_row').toggle();
                $('html, body').animate({scrollTop: 0}, 'fast');
                $('#tablefilters').find('input:first').focus();
                return false;
            }
        });
    }

    $("input[id^='pickDate']").live('click', function() {
        //$(this).removeClass('hasDatepicker');
        $(this).datepicker({altField: "#alt" + $(this).attr('id'), altFormat: 'dd-mm-yy', dateFormat: 'MM dd, yy', showWeek: true, firstDay: 1, changeMonth: true, changeYear: true, showAnim: 'slideDown'}).focus();
        $("#ui-datepicker-div").css("z-index", $(this).parents(".ui-dialog").css("z-index") + 1);

    });

    $("input[id$='_autocomplete']").bind("keydown", function() {
        if(sharedFunctions.checkSession() == false) {
            return;
        }
        var id = $(this).attr("id").split("_");
        $(this).autocomplete({
            source: function(request, response) {
                var cache = {};
                var term = request.term;

                if(id[id.length - 1] === 'cache') {
                    if(term in cache) {
                        response(cache[ term ]);
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

                    $("input[id^='" + id[0] + "']" + inputselection_extra + "[id$='_id']").each(function() {
                        if($(this).val().length > 0) {
                            exclude += comma + $(this).val();
                            if(++count != 1) {
                                comma = ",";
                            }
                        }
                    });
                }

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

                $.getJSON("../search.php?type=quick&returnType=json&for=" + id[0] + "&exclude=" + exclude + filtersQuery, {
                    value: term
                }, function(data, status, xhr) {
                    console.log(data);
                    if(id[id.length - 1] === 'cache') {
                        cache[ term ] = data;
                    }
                    response(data);
                });
            },
            minLength: 2,
            select: function(event, ui) {
                console.log(ui);
                if($("#" + id[0] + "_" + id[id.length - 3] + "_" + id[id.length - 2] + "_id").length > 0) {
                    var valueIn = "#" + id[0] + "_" + id[id.length - 3] + "_" + id[id.length - 2] + "_id";
                }
                else if($("#" + id[0] + "_" + id[id.length - 2] + "_id").length > 0) {

                    var valueIn = "#" + id[0] + "_" + id[id.length - 2] + "_id";
                }
                else {
                    var valueIn = "#" + id[0] + "_id";
                }
                alert(valueIn);
                $(valueIn).val(ui.item.id);
                $(valueIn + "_output").val(ui.item.id);
            }
        });
    });
    $("input[id$='_QSearch']").live("keyup", QSearch);

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

            $("input[id^='" + id[0] + "']" + inputselection_extra + "[id$='_id']").each(function() {
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
            $.post(rootdir + "search.php?type=quick&for=" + id[0] + "&exclude=" + exclude + filtersQuery,
                    {value: "" + inputValue + ""},
            function(returnedData) {
                if(returnedData.length > 0) {
                    $(resultsIn).html(returnedData);
                    $(resultsIn).slideDown();

                    $("#searchResultsList > li").click(function() {
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

                    $(document).not("input[id='" + id[0] + "_" + id[1] + "_QSearch']").click(function() {
                        $(resultsIn).slideUp().empty();
                    });
                }
            });
        }
        inputValue = "";
    }

    $("input.ajaxcheckbox").live('change', function() {
        if(sharedFunctions.checkSession() == false) {
            return;
        }

        var id = $(this).attr("id").split("_");
        if($(this).is(':checked') == false) {
            $(this).val('0');
        }

        sharedFunctions.requestAjax("post", "index.php?module=" + id[id.length - 2], 'action=' + id[0] + '&value=' + $(this).val() + '&id=' + id[1], $(this).attr("id") + '_Result', $(this).attr("id") + '_Result');
    });

    $(document).on("click", "input[type='submit'][id$='_Button']", function() {
        var id = $(this).attr("id").split("_");
        var formid = '';
        for(var i = 0; i < id.length - 1; i++) {
            formid += id[i] + "_";
        }

        $("form[id='" + formid + "Form']:not([action]),form[id='" + formid + "Form'][action='#']").submit(function(e) {
            e.preventDefault();
        });
    });

    $(document).on("click", "input[id^='perform_'][id$='_Button'],input[id^='add_'][id$='_Button'],input[id^='change_'][id$='_Button']", function() {
        if(sharedFunctions.checkSession() == false) {
            return;
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

        sharedFunctions.requestAjax("post", url, formData, formid + "Results", formid + "Results");
    });

    $("a[id^='showmore_'][href^='#']").click(function() {
        var id = $(this).attr("id").split("_");
        $("#" + id[1] + "_" + id[2]).toggle();
    });


    $("img[id^='addmore_']").live('click', function() {
        sharedFunctions.addmoreRows($(this));
    });

    $("input[id='email'],input[accept='email']").live("keyup", validateEmailInline);
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

    $("input[type='button'][id$='_swap']").click(function() {
        var id = $(this).attr("id").split("_");
        if($("#" + id[0] + "_last").val().length > 0) {
            $("#" + id[0]).val($("#" + id[0] + "_last").val());
        }
    });

    $("input[accept='numeric']").live("keydown", function(e) {
        if(e.keyCode > 31 && (e.keyCode < 48 || (e.keyCode > 57 && (e.keyCode < 96 || e.keyCode > 105) && e.keyCode != 190 && e.keyCode != 110 && e.keyCode != 16 && e.keyCode != 17 && e.keyCode != 59))) {
            //$(this).val($(this).val().substring(0, ($(this).val().length - 1)));
            e.preventDefault();
            return false
        }
        return true
    });

    $("a[id='resetpassword']").live('click', function() {
        $("#logincontent").hide();
        $("#resetpasswordcontent").show();
    });

    $("#resetpassword_Button").click(function() {
        sharedFunctions.requestAjax("post", "users.php?action=reset_password", "email=" + $("input[id='email']").val(), "resetpassword_Results", "resetpassword_Results");
    });

    $("#changepassword_Button").click(function() {
        sharedFunctions.requestAjax("post", "users.php?action=do_changepassword", $("form[id='changepassword_Form']").serialize(), "changepassword_Results", "changepassword_Results");
    });

    $("#modifyprofile_Button").click(function() {
        var formData = $("form[id='modifyprofile_Form']").serialize();
        sharedFunctions.requestAjax("post", "users.php?action=do_modifyprofile", formData, "modifyprofile_Results", "modifyprofile_Results");
    });

    $("input[id='getReports']").click(function() {
        if(sharedFunctions.checkSession() == false) {
            return;
        }
        $.post("index.php?module=reporting/createreports&action=get_reports",
                {quarter: $("#quarter").val(), year: $("#year").val()},
        function(returnedData) {
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
        function(returnedData) {
            if($("status", returnedData).text() == 'true') {
                var spanClass = 'green_text';
            } else {
                var spanClass = 'red_text';
            }

            $("#login_Results").html("<span class='" + spanClass + "'>" + $("message", returnedData).text() + "</span>");

            if($("#noredirect").val() != '1') {
                if($("status", returnedData).text() == "true") {
                    goToURL($("#referer").val());
                }
            }
        },
                'xml'
                );
    }

    $("a[id$='_loadpopupbyid'],a[id^='mergeanddelete_'][id$='_icon'],a[id^='revokeleave_'][id$='_icon'],a[id^='approveleave_'][id$='_icon']").live('click', function() {
        var id = $(this).attr("id").split("_");
        popUp(id[2], id[0], id[1]);
    });

    $(".showpopup,input[id^='showpopup_']").live("click", function() {
        var id = $(this).attr("id").split("_");
        $('#popup_' + id[1]).dialog('open');
    });

    if($("div[id^='popup_']").length > 0) {
        $("div[id^='popup_']").dialog({
            autoOpen: false,
            bgiframe: true,
            closeOnEscape: true,
            modal: true,
            width: 500,
            maxWidth: 500,
            close: function() {
                $(this).find("form").each(function() {
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

    $("a[id^='addnew_']").live("click", function() {
        var id = $(this).attr("id").split("_");
        popUp(id[1], id[0] + "_" + id[2]);
    });

    $('input[title],a[title],div[title],span[title]').qtip({style: {classes: 'ui-tooltip-green ui-tooltip-shadow'}, show: {event: 'focus mouseenter', solo: true}, hide: 'unfocus mouseleave', position: {viewport: $(window)}});

    function popUp(module, template, id) {
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

        /*change ajax call*/
        $.ajax({type: 'post',
            url: file + "?module=" + module + "&action=get_" + template,
            data: "id=" + id,
            beforeSend: function() {
                $("body").append("<div id='modal-loading'><span  style='display:block; width:100px; height: 100%; margin: 0 auto;'><img  src='./images/loader.gif'/></span></div>");
                $("#modal-loading").dialog({height: 150, modal: true, closeOnEscape: false, title: 'Loading...', resizable: false, minHeight: 0

                });
            },
            complete: function() {
                $("#modal-loading").dialog("close").remove();
            },
            success: function(returnedData) {
                $(".contentContainer").append(returnedData);

                $("div[id^='popup_']").dialog({
                    bgiframe: true,
                    closeOnEscape: true,
                    modal: true,
                    width: 460,
                    maxWidth: 460,
                    zIndex: 1000,
                    close: function() {
                        $(this).find("form").each(function() {
                            this.reset();
                        });
                        $(this).find("span[id$='_Validation']").empty();
                        $(this).find("span[id$='_Results']").empty();
                        $(this).remove();
                    }
                });
                //$("#popupBox").html(returnedData).show("slow");
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
                $("input[id='hide_popupBox']").click(function() {
                    $("#popupBox").hide("fast");
                });
            }

        });
        // $.post(file,
        // {module: module, action: "get_" + template, id: id},

        //);
    }

    $("a[href='#'][id^='approve_']").click(function() {
        if(sharedFunctions.checkSession() == false) {
            return;
        }
        var details = $(this).attr("id").split("_");
        var data = "&action=do_" + details[0] + "&attribute=" + details[2] + "&newvalue=" + details[3] + "&id=" + details[4];

        sharedFunctions.requestAjax("post", "index.php?module=" + details[1], data, $(this).attr("id"), $(this).attr("id"));
    });

    $("input[class='inlineCheck']").blur(function() {
        if($(this).val().length != 0) {
            var parentId = $(this).parents("form").attr("id").split("_");

            var results = $(this).attr("id") + "_inlineCheckResult";
            var data = "&action=inlineCheck&attr=" + $(this).attr("name") + "&value=" + $(this).val();
            sharedFunctions.requestAjax("post", "index.php?module=" + parentId[1], data, results, results);
        }
    });

    $("img[id^='ajaxaddmore_']").live("click", function() {
        if(sharedFunctions.checkSession() == false) {
            return;
        }

        var id = $(this).attr('id').split('_');
        var num_rows = 0;
        if($("#numrows_" + id[id.length - 2] + id[id.length - 1]).length != 0) {
            var num_rows = parseInt($("#numrows_" + id[id.length - 2] + id[id.length - 1]).val());
            var affid = parseInt($("#affid_" + id[id.length - 2] + id[id.length - 1]).val());
        }

        $.ajax({type: 'post',
            url: rootdir + "index.php?module=" + id[1] + "&action=ajaxaddmore_" + id[2],
            data: "value=" + num_rows + "&id=" + id[id.length - 1] + "&" + $('input[id^=ajaxaddmoredata_]').serialize(),
            beforeSend: function() {
                $("body").append("<div id='modal-loading'></div>");
                $("#modal-loading").dialog({height: 0, modal: true, closeOnEscape: false, title: 'Loading...', resizable: false, minHeight: 0
                });
            },
            complete: function() {
                $("#modal-loading").dialog("close").remove();
            },
            success: function(returnedData) {
                $('#' + id[id.length - 2] + id[id.length - 1] + '_tbody').append(returnedData);
                if($("#numrows_" + id[id.length - 2] + id[id.length - 1]).length != 0) {
                    $("#numrows_" + id[id.length - 2] + id[id.length - 1]).val(num_rows + 1);
                }
                /*find the offset of the first input in the last tr*/
                $("html, body").animate({scrollTop: $('#' + id[id.length - 2] + id[id.length - 1] + '_tbody > tr:last').find("input").filter(':visible:first').offset().top}, 1000);
                $('#' + id[id.length - 2] + id[id.length - 1] + '_tbody > tr:last').effect("highlight", {color: '#D6EAAC'}, 1500).find('input').first().focus();
            }
        });

    });

    window.sharedFunctions = function() {
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
            if(options == 'animate') {
                var image_name = 'ajax-loader.gif';
            }

            $.ajax({type: methodParam,
                url: urlParam,
                data: dataParam,
                beforeSend: function() {
                    $("div[id='" + loadingId + "'],span[id='" + loadingId + "']").html("<img style='padding: 5px;' src='" + imagespath + "/" + image_name + "'' alt='" + loading_text + "' border='0' />");
                },
                complete: function() {
                    if(loadingId != contentId) {
                        $("#" + loadingId).empty();
                    }
                },
                success: function(returnedData) {
                    console.log(returnedData);
                    if(datatype == 'xml') {
                        if($(returnedData).find('status').text() == 'true') {
                            var spanClass = 'green_text';
                        } else {
                            var spanClass = 'red_text';
                        }

                        $("div[id='" + contentId + "'],a[id='" + contentId + "'],span[id='" + contentId + "']").html("<span class='" + spanClass + "'><img src='" + imagespath + "/" + $(returnedData).find('status').text() + ".gif' border='0' />&nbsp;" + $(returnedData).find('message').text() + "</span>");

                    }
                    else
                    {
                        $("#" + contentId).html($.trim(returnedData));
                        if(options != "undefined") {
                            if(options == 'animate') {
                                $("#" + contentId).slideDown("slow");
                                // If successful, bind 'loaded' in the data
                                $("#" + contentId).data('dataloaded', true)
                            }
                        }
                    }
                },
                dataType: datatype
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
             $(this).attr("name", $(this).attr("name").replace(last, increment.toString()));
             });
             }*/
            var needed_attributes = ["id", "name"];
            $("#" + id[1] + "_tbody > div").scrollTop();
            $.each(needed_attributes, function(key, val) {
                //$("#"+ id[1] +"_tbody > tr[id='" + increment + "']").find("input,select,div[id],span,textarea[name],img[id],tbody").each(function() {

                $("#" + id[1] + "_tbody > tr[id='" + increment + "']").find("tr[" + val + "],input[" + val + "],select[" + val + "],div[" + val + "],span[" + val + "],textarea[" + val + "],img[" + val + "],tbody[" + val + "]").each(function() {
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
                });
            });
            if(id[1] == 'keycustomers') {
                $("#" + id[1] + "_tbody > tr:last").find("td:first").html($("#" + id[1] + "_tbody tr:last").find("td:first").html().replace(last, increment.toString()));
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
        return {
            "requestAjax": requestAjax,
            "checkSession": checkSession,
            "addmoreRows": addmoreRows,
            "sharedPopUp": sharedPopUp
        }
    }();
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