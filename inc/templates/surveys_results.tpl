<head>
<title>{$core->settings[systemtitle]} | {$survey[subject]} {$lang->responses}</title>
{$headerinc}
<script>
$(function() {
	$("[id^='getquestionresponses_']").click(function() {
		if(sharedFunctions.checkSession() == false) {
			return;	
		}
		var id =  $(this).attr("id").split("_");
			
		sharedFunctions.requestAjax("post", "index.php?module=surveys/viewresults&action=get_questionresponses", "question=" + id[1] +"&identifier=" + id[2],'questionresponses_results_'+ id[1], 'questionresponses_results_'+ id[1], 'html');
                
             // $("[id^='getquestionresponses_"+ id[1]+"']").unbind("click"); /*prevent multiple ajax request by remove click event */

    });	
  	$("[id^='sendreminder_']").click(function() {
		if(sharedFunctions.checkSession() == false) {
			return;	
		}
		var id =  $(this).attr("id").split("_");
	
		sharedFunctions.requestAjax("post", "index.php?module=surveys/viewresults&action=sendreminder", "&identifier=" + id[1],'','remindermsg','html');
               
            // $("[id^='sendreminder_]").unbind("click"); /*prevent multiple ajax request by remove click event */

    });	
});
</script>
</head>
<body>
{$header}
<tr>
{$menu}
<td class="contentContainer">
    <h3>{$survey[subject]}</h3>
	{$questionsstats}
        <div class="subtitle" style="float:right;"><span id="remindermsg"></span><input type="button" id="sendreminder_{$core->input[identifier]}"  style=" margin: 5px;" class="button" value="{$lang->sendreminder}"/></div>
    {$responses}
	{$invitations}   
</td> 
</tr>
{$footer}
</body>
</html>