<script type="text/javascript">
    $(function() {
        $(document).on('change', "select[id$='_[type]']", function() {
            if(sharedFunctions.checkSession() == false) {
                return;
            }
            var id = $(this).attr("id").split("_");
            if($(this).val() != "") {
                $.ajax({
                    type: 'post',
                    url: rootdir + "index.php?module=surveys/createsurveytemplate&action=parsetype",
                    data: {
                        questiontype: $("select[id^='section_" + id[1] + "_[questions]_" + id[3] + "_[type]']").val(), sectionid: id[1], questionid: id[3]},
                    beforeSend: function() {
                        $("select[id^='section_" + id[1] + "_[questions]_" + id[3] + "_[type]']").after("<img id='section_" + id[1] + "_[questions]_" + id[3] + "_[type][loading]' style='padding: 5px;' src='" + imagespath + "/loading-bar.gif' alt='" + loading_text + "' border='0' />");
                    },
                    complete: function() {
                        $("img[id='section_" + id[1] + "_[questions]_" + id[3] + "_[type][loading]']").remove();
                    }
                });
            }
        });
        $(document).on('change', "select[id$='[validationType]']", function() {
            var id = $(this).attr("id").split("_");
            var valMatch = ["minchars", "maxchars"];
            $("tr[id='section_" + id[1] + "_[questions]_" + id[3] + "_[validationCriterion]']").css("display", "none");
            if(jQuery.inArray($(this).val(), valMatch) != -1) {
                $("tr[id='section_" + id[1] + "_[questions]_" + id[3] + "_[validationCriterion]']").css("display", "table-row");
            }
        });
        $(document).on('change', "input[id^='isQuiz_']", function() {
            var val = $(this).val();
            var show = 0;
            if(val == 1) {
                show = 1;
            }
            $('input[id^="ajaxaddmoredata_questionschoices_"]').each(function(i, obj) {
                $(obj).val(val);
            });
            $('input[name="ajaxaddmoredata[type]"]').each(function(i, obj) {
                $(obj).val(val);
            });
            $('td[id^="answer_"]').each(function(i, obj) {
                if(show == 1) {
                    $(obj).show();
                } else {
                    $(obj).hide();
                }
            });
        });
        $(document).on('click', "input[id='test']", function() {
            if($("#popup_createbasedonanothertpl").dialog("isOpen")) {
                var stid = $("select[id='stid']").val();
                $("#popup_createbasedonanothertpl").dialog("close");
            }
            var url = window.location.href;
            url = url.replace('#', '') + '&bstid=' + stid;
            window.location.href = url;
        });
        $(document).on('click', "img[id^='deletesection_']", function() {
            if(sharedFunctions.checkSession() == false) {
                return;
            }
            $('div[id^="sectiondeleteresult_"]').each(function(i, obj) {
                $(obj).html('');
            });
            var extrainput = '';
            var id = $(this).attr('id').split('_');
            var url = rootdir + "index.php?module=surveys/createsurveytemplate&action=delete_section";
            if($('input[id="sectionid_' + id[1] + '"]').val()) {
                extrainput = "&sectionid=" + $('input[id="sectionid_' + id[1] + '"]').val();
            }
            if($('input[id="sectionchecksum_' + id[1] + '"]').val()) {
                extrainput += "&checksum=" + $('input[id="sectionchecksum_' + id[1] + '"]').val();
            }
            $.ajax({
                type: 'post',
                url: url,
                data: "rowid=" + id[1] + extrainput,
                beforeSend: function() {
                    $("body").append("<div id='modal-loading'></div>");
                    $("#modal-loading").dialog({height: 0, modal: true, closeOnEscape: false, title: 'Loading...', resizable: false, minHeight: 0});
                },
                complete: function() {
                    $("#modal-loading").dialog("close").remove();
                },
                success: function(returnedData) {
                    $('#sectiondeleteresult_' + id[1]).append(returnedData);
                }
            });
        });
    });
    $(document).ajaxSuccess(function() {
        $("tbody[id^='questions'][id$='_tbody']").sortable({placeholder: "ui-state-highlight", forcePlaceholderSize: true, delay: 300, opacity: 0.5, containment: "parent", handle: '.questions-sort-icon'});
    });

</script>
<h1>{$lang->createtemplate} </h1>
<form name="perform_surveys/createsurveytemplate_Form" id="perform_surveys/createsurveytemplate_Form" action="#" method="post">
    <input type="hidden" id="action" name="action" value="{$action}" />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>{$lang->createbasedonanother}</td>
            <td>

                <a href="#" id="createbasedonanother_{$values['eptid']}_surveys/createsurveytemplate_loadpopupbyid" title="Clone">
                    <img src="{$core->settings['rootdir']}/images/clone.gif" border="0"/>
                </a>
            </td>
        </tr>
        <tr><td colspan='2'><span class="subtitle">{$lang->basictemplateinfo}</span></td></tr>
        <tr>
            <td width="20%">{$lang->surveytemplatetitle}</td>
            <td width="80%"><input name="title" id="title" type="text" size="30" required="requierd"></td>
        </tr>
        <tr>
            <td>{$lang->category}</td>
            <td>
                {$surveycategories_list}
            </td>
        </tr>
        <tr>
            <td>{$lang->publicallyavailable}</td>
            <td>{$radiobuttons[isPublic]}</td>
        </tr>
        <tr>
            <td>{$lang->forceanonymousfilling}</td>
            <td>{$radiobuttons[forceAnonymousFilling]}</td>
        </tr>
        <tr>
            <td>{$lang->quiz}</td>
            <td>{$radiobuttons[isQuiz]}</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left; padding:0; margin:0px;">
                <table width="100%">
                    <thead>
                        <tr>
                            <td colspan="2"><hr /><span class="subtitle">{$lang->surveyquestions}</span></td>
                        </tr>
                    </thead>
                    <tbody id="section_{$section_rowid}_tbody">
                        {$newsection}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"><img src="./images/add.gif" id="ajaxaddmore_surveys/createsurveytemplate_section_{$section_rowid}"  border="0" alt="{$lang->add}">
                                <input name="numrows_section{$section_rowid}" type="hidden" id="numrows_section_{$section_rowid}" value="{$section_rowid}">
                                <input type="hidden" name="ajaxaddmoredata[type]" id="ajaxaddmoredata_section_{$section_rowid}" value="{$survery_template[isQuiz]}"/>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
        <tr>
            <td>{$lang->openpreview}<input type="checkbox" name="preview" value="1"></td>
        </tr>
        <tr>
            <td colspan="3">
                <hr />
                <input  type="submit" value="{$lang->$action}" id="perform_surveys/createsurveytemplate_Button" tabindex="26" class="button"/>

                <input type="reset" value="{$lang->reset}" class="button" />
            </td>
        </tr>
    </table>
    <div id="perform_surveys/createsurveytemplate_Results"></div>
</form>