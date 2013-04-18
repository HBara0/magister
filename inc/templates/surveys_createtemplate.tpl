<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->createsurveytemplate}</title>
{$headerinc}
<script type="text/javascript">
	$(function get_question_type() {	
		$("select[id^='section']").live('change', function() {
			if(sharedFunctions.checkSession() == false) {
				return;	
			}	
			var id = $(this).attr("id").split("_");
			$.post("index.php?module=surveys/createsurveytemplate&action=parsetype", {
			questiontype:$("select[id^='section_"+id[1]+"_[questions]_"+id[3]+"_[type]']").val(),sectionid:id[1],questionid:id[3]},
			function(returnedData){alert(returnedData);}
				);	
		});	
	});
</script>
</head>
<body>
{$header}
<tr>
    {$menu}
    <td class="contentContainer">
        <h3>{$lang->createsurveytemplate}</h3>
        <form name="perform_surveys/{$action}_Form" id="perform_surveys/{$action}_Form" action="#" method="post">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="20%">{$lang->surveytemplatetitle}</td>
                <td width="80%"><input name="title" id="title" type="text" size="30"></td>
            </tr>
            <tr>
                <td>{$lang->category}</td>
                <td>
                    <select name="category">
                      <option value="1">{$lang->other}</option>
                      <option value="2">{$lang->supplierevaluation}</option>
                      <option value="3">{$lang->employeeevaluation}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>{$lang->publicfill}</td>
                <td>{$radiobuttons[isPublic]}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">{$lang->forceanonymousfilling}</td>
                <td>{$radiobuttons[forceanonymousfilling]}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: left; padding:0; margin:0px;">
                    <table width="100%">
                        <thead>
                            <tr>
                                <td colspan="2" class="thead"><div style="width:40%; display:inline-block;" align="center">{$lang->section}</div></td>
                            </tr>
                        </thead>
                        <tbody id="section{$section_rowid}_tbody">
                            {$newsection}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"><img src="./images/add.gif" id="ajaxaddmore_surveys/createsurveytemplate_section_{$section_rowid}"  border="0" alt="{$lang->add}"><input name="numrows_section{$section_rowid}" type="hidden" id="numrows_section{$section_rowid}" value="{$section_rowid}"></td>
                            </tr>
                        </tfoot>
                    </table>    
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <hr />
                    <input type="button" value="{$lang->$action}" id="perform_surveys/{$action}_Button" tabindex="26" class="button"/>
                    <input type="reset" value="{$lang->reset}" class="button" />
                </td>
            </tr>
        </table>
        <div id="perform_surveys/{$action}_Results"></div>  
        </form>  
    </td>
</tr>
{$footer}
</body>
</html>