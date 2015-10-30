<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->managewindows}</title>
        {$headerinc}
        <script>
            $(function () {
                var tabs = $("#sectionstabs").tabs();
                var tabcounter = tabs.find(".ui-tabs-nav").find('li').length + 1; //find the  lenght of li tabs and increment by 1
                $(document).on('change', 'select[id^="field"][id$="[fieldType]"]', function () {
                    var id = $(this).attr('id').substring(0, $(this).attr('id').indexOf('[fieldType]')) + '[srliid]';
                    if($(this).val() == 'list') {
                        $("div[id='" + id + "']").show();
                    }
                    else {
                        $("div[id='" + id + "']").hide();
                    }
                });
                $(document).on('click', "#createtab", function () {
                    var templatecontent = errormessage = '';
                    var id = "sectionstabs-" + tabcounter;
                    if($("input[id='window_id']").val().length == 0) {
                        return;
                    }
                    var windowid = $("input[id='window_id']").val();
                    /*User cannot add a new section if the destination city/to date of the previous section are not filled*/
                    $('div[id=sectionstabs-' + tabcounter + ']').remove(); //remove  Error about destination city and date
                    var label = "section " + tabcounter;
                    // tabTemplate = "<li><a href='#" + id + "'>" + label + "</a></li>"
                    tabTemplate = "<li><a href='#" + id + "'>" + label + "</a> <span class='ui-icon ui-icon-close' role='presentation' title='Close'>Remove Tab</span></li>"
                    tabs.find(".ui-tabs-nav").append(tabTemplate);
                    /* get content thought ajax*/
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }
                    /*Select the  tabs-panel that isn't hidden with  tabs-hide:*/
                    var selectedPanel = $("#sectionstabs div.ui-tabs-panel:not(.ui-tabs-hide)");
                    var templatecontent = sharedFunctions.requestAjax("post", "index.php?module=managesystem/managewindows&action=add_section&windowid=" + windowid, "sequence=" + tabcounter, 'loadindsection', id, 'html', true);
                    var templatecontent = errormessage = '';
                    tabs.append("<div id=" + id + "><p>" + templatecontent + "</p></div>");
                    tabs.tabs("refresh");
                    $("#sectionstabs").tabs("option", "active", (tabcounter) - 1);
                    tabcounter = tabcounter + 1;
                });
                $(document).on("click", "input[id^='windows_'][id$='_Button'],input[id^='sections_'][id$='_Button'],input[id^='fields_'][id$='_Button']", function () {
                    if(sharedFunctions.checkSession() == false) {
                        return;
                    }
                    for(instance in CKEDITOR.instances) {
                        CKEDITOR.instances[instance].updateElement();
                    }
                    var selectedform = $(this).attr('form');
                    var id = $(this).attr("id").split("_");
                    // var formid = id[0] + "_" + id[2];
                    var formid = '';
                    for(var i = 0; i < id.length - 1; i++) {
                        formid += id[i] + "_";
                    }
                    var formData = $("form[id='" + selectedform + "']").serialize();
                    var details = id[id.length - 2].split("/");
                    var url = "index.php?module=" + id[id.length - 2];
                    if(!formData.match(/action=[A-Za-z0-9]+/)) {
                        url += "&action=save_" + id[0] + "_" + details[1];
                    }
                    sharedFunctions.requestAjax("post", url, formData, formid + "Results", formid + "Results");
                });
                tabs.delegate("span.ui-icon-close", "click", function () {
                    /*only send ajax request when segmentid exist on modify*/
                    if(typeof $(this).closest("li").attr("aria-controls") !== typeof undefined && $(this).closest("li").attr("aria-controls") !== false) {
                        var sectionid = $(this).closest("li").attr("aria-controls").split("-");
                        sharedFunctions.requestAjax("post", "index.php?module=managesystem/managewindows&action=deletesection", "&sectionid=" + sectionid[1], '', '', true);

                    }
                    var panelId = $(this).closest("li").remove().attr("aria-controls");
                    $("#" + panelId).remove();
                    tabcounter = tabcounter - 1;
                    tabs.tabs("refresh");
                    //   $('input[id="perform_travelmanager/plantrip_Button"]').click();
                });
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>{$lang->managewindows}</h1>
            <div>
                <form name="windows_1_managesystem/managewindows_Form" id="windows_1_managesystem/managewindows_Form" action="#" method="post">
                    <table>
                        <tr>
                            <td>{$lang->name}</td><td><input type='text' name='window[name]' value='{$window['name']}'></td>
                            <td>{$lang->type}</td><td>{$window_type_list}</td>
                            <td>{$lang->title}</td><td><input type="text" value="{$window['title']}" name="window[title]"></td>
                            <td>{$lang->isactive}</td><td><input type="checkbox" name='window[isActive]' value="1" {$window_isactive_check}></td>
                        </tr>
                        <tr>
                            <td>{$lang->description}</td><td><textarea name="window[description]">{$window['description']}</textarea></td>
                        </tr>
                        <tr>
                            <td>{$lang->helpcomments}</td><td><textarea name="window[comments]">{$window['comments']}</textarea></td>
                        </tr>
                    </table>
                    <input type='submit' style="cursor: pointer" form="windows_1_managesystem/managewindows_Form" class='button' value="{$lang->savewind}" id='windows_1_managesystem/managewindows_Button'>
                </form>
                <div id="windows_1_managesystem/managewindows_Results"></div>
                {$sections}
            </div>
        </td>
    </tr>
    {$footer}
</body>
</html>