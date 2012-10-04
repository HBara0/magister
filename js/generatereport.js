$(function() {	  
	$("#generateType,#generateType2").change(function() {
		if($("input[name='generateType']:checked").val() == 1) {
			$("select[id='affid']").attr("multiple", "true");
			$("select[id='affid']").attr("size", "5");	
			$("select[id='affid']").attr("name", "affid[]");  
			$("select[id='spid']").removeAttr("multiple");
			$("select[id='spid']").removeAttr("size");
			$("select[id='spid']").attr("name", "spid");
		}
		else
		{
			$("select[id='spid']").attr("multiple", "true");
			$("select[id='spid']").attr("size", "5");
			$("select[id='spid']").attr("name", "spid[]"); 
			$("select[id='affid']").removeAttr("multiple");
			$("select[id='affid']").removeAttr("size");
			$("select[id='affid']").attr("name", "affid");
		}
	});
	
	$("#quarter,#year,#affid,#spid").change(getMoreData);
	
	/*$("#affid,#spid").change(function() {
		if($(this).val() == '0') {
			$("#buttons_row").hide();	
		}
		else
		{
			$("#buttons_row").show();
		}
	});*/
	
	function getMoreData() {
		if(sharedFunctions.checkSession() == false) {
			return;	
		}
		
		var value = $(this).val();
		
		if(value != '0') {
			var id = $(this).attr("id");
			var dataParam = "id=" + value;
			var get = "";
			
			if(id == "affid") { 
				if($("input[name='generateType']:checked").val() == 2) {
					dataParam += "&quarter=" + $("#quarter").val() + "&year=" + $("#year").val();
					get = "supplierslist";
					loadingIn = "supplierslist_Loading";
					contentIn = "spid";
					$("#spid").empty();
					$("#buttons_row").hide();	
				}
				else
				{
					$("#buttons_row").show();
					return false;	
				}
			}
			else if(id == "spid")
			{
				if($("input[name='generateType']:checked").val() == 1) {
					dataParam += "&quarter=" + $("#quarter").val() +  "&year=" + $("#year").val();
					get = "affiliateslist";	
					loadingIn = "affiliateslist_Loading";
					contentIn = "affid";
					$("#affid").empty();
					$("#buttons_row").hide();	
				}
				else
				{
					$("#buttons_row").show();
					return false;	
				}
			}
			else if(id == "quarter") {
				dataParam += "&affid=" + $("#affid").val() + "&spid=" + $("#spid").val();
				get = "years";	
				loadingIn = "years_Loading";
				contentIn = "year";
				$("#year,#affid,#spid").empty();
			}
			else if(id == "year") {
				dataParam += "&quarter=" + $("#quarter").val();
				if($("input[name='generateType']:checked").val() == 2) {
					get = "affiliateslist";	
					loadingIn = "affiliateslist_Loading";
					contentIn = "affid";
				}
				else
				{
					get = "supplierslist";	
					loadingIn = "supplierslist_Loading";
					contentIn = "spid";	
				}
				$("#affid,#spid").empty();	
			}
			$("#buttons_row").hide();
			var url = "index.php?module=reporting/generatereport&action=get_" + get;
			
			$.ajax({method: "post",
					url: url,
					data: dataParam,
					beforeSend: function() { $("#" + loadingIn).html("<img src='" + imagespath +"/loading.gif' alt='" + loading_text + "'/>")},
					complete: function () { $("#" + loadingIn).empty();},
					success: function(returnedData) {
						$("#" + contentIn).html(returnedData);
						
						if(contentIn == 'affid') {
							if($("input[name='generateType']:checked").val() == 1) {
								$("#affid option").each(function(){
									if($(this).val() != '0') {
										$(this).attr("selected","selected");
									}
								});
								$("#buttons_row").show();
							}
						}
						else 
						{
							if(contentIn == 'spid') {
								if($("input[name='generateType']:checked").val() == 2) {
									$("#spid option").each(function(){
										if($(this).val() != '0') {
											$(this).attr("selected","selected");
										}
									});
									$("#buttons_row").show();
								}
							}
						}
					}
			});
		}
		else
		{
			$("#buttons_row").hide();	
		}
	}
});