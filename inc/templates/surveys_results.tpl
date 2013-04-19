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
			
		//sharedFunctions.requestAjax("post", "index.php?module=surveys/viewresults&action=get_questionresponses", "question=" + id[1] +"&identifier=" + id[2],'questionresponses_results_'+ id[1], 'questionresponses_results_'+ id[1], 'html');			
            
         $.ajax({ type: 'post',
           url: "index.php?module=surveys/viewresults&action=get_questionresponses",
            data: {question: id[1], identifier: id[2]}, 
            beforeSend: function() {
                $("body").append("<div id='modal-loading'></div>");
                $("#modal-loading").dialog({ height: 0, modal: true, closeOnEscape: false, title: 'Loading...', resizable: false, minHeight: 0 
            });
            },
            complete: function() {
                $("#modal-loading").dialog("close").remove();
            },
              success: function(returnedData) { alert(returnedData);
            $('#questionresponses_results_'+ id[1]).append(returnedData);
              //$('#questionresponses_results_'+ id[1]).append(returnedData);
              }
        })
 
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
    {$responses}
	{$invitations}   
</td> 
</tr>
{$footer}
</body>
</html>