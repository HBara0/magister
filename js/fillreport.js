$(function() {	  
	$("#affid,#spid,#quarter").change(getMoreData);
	
	$("#year").change(function() {
		if($(this).val() == '0') {
			$("#buttons_row").hide();	
		}
		else
		{
			$("#buttons_row").show();
		}
	});
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
				get = "supplierslist";
				loadingIn = "supplierslist_Loading";
				contentIn = "spid";
				$("#spid,#quarter,#year").empty();
			}
			else if(id == "spid")
			{
				dataParam += "&affid=" + $("#affid").val();
				get = "quarters";
				loadingIn = "quarters_Loading";
				contentIn = "quarter";
				$("#quarter,#year").empty();
			}
			else if(id == "quarter") {
				dataParam += "&affid=" + $("#affid").val() + "&spid=" + $("#spid").val();
				get = "years";	
				loadingIn = "years_Loading";
				contentIn = "year";
				$("#year").empty();
			}
			$("#buttons_row").hide();
			var url = "index.php?module=reporting/fillreport&action=get_" + get;
			
			$.ajax({method: "post",
					url: url,
					data: dataParam,
					beforeSend: function() { $("#" + loadingIn).html("<img src='" + imagespath +"/loading.gif' alt='" + loading_text + "'/>")},
					complete: function () { $("#" + loadingIn).empty();},
					success: function(returnedData) {
						$("#" + contentIn).html(returnedData);			
					}
			});
		}
		else
		{
			$("#buttons_row").hide();	
		}
	}
		
	$("form[id='save_productsactivity_reporting/fillreport_Form']").submit(function() { return validateEmpty('productsactivity'); });
	
	$("input[id^='turnOver_'],input[id^='salesForecast_'],input[id^='quantityForecast_'],input[id^='quantity_']").live("click", function() {
		$(this).blur(function() {
			var id = $(this).attr("id").split("_");
			var toEvaluate = "";
			var evaluationType = "smaller";
			var evaluationConversion = 1;
			
			switch(id[0]) {
			case "turnOver": 
					toEvaluate = "salesForecast";
					evaluationConversion = parseFloat($('#fxrate_'+id[1]+' option:selected').val());
					break;
			case "salesForecast": 
					toEvaluate = "turnOver";
					evaluationType = "greater";
					evaluationConversion = parseFloat($('#fxrate_'+id[1]+' option:selected').val());
					break;
			case "quantityForecast": 
					toEvaluate = "quantity";
					evaluationType = "greater";
					break;
			case "quantity": 
					toEvaluate = "quantityForecast";
					break;
			}
			
			if($("#" + toEvaluate + "_" + id[1]).val() != '') {
				if(evaluationType == "smaller") {
					if((parseFloat($(this).val())/evaluationConversion) > parseFloat($("#" + toEvaluate + "_" + id[1]).val())) {
						$(this).val(($("#" + toEvaluate + "_" + id[1]).val()*evaluationConversion).toFixed(3));	
					}
				}
				else
				{
					if(parseFloat($(this).val()) < (parseFloat($("#" + toEvaluate + "_" + id[1]).val())/evaluationConversion)) {
						$(this).val(($("#" + toEvaluate + "_" + id[1]).val()/evaluationConversion).toFixed(3));	
					}
				}
			}
		});
		$(this).change(function() { 
			if($(this).val() > 1000) {
				$('#numbernotificationbox').remove();
				$(".contentContainer").append('<div id="numbernotificationbox">Are you sure that this number is correct?<p><strong>Please review it.</strong></p></div>');
				$("div[id='numbernotificationbox']").dialog({ 
						bgiframe: true,
						closeOnEscape: true,
						modal: true,
						width: 300,
						maxWidth: 300,
						height: 100,
						zIndex: 1,
						buttons: {
							'Proceed': function() {
								$(this).dialog('close');
							}
						},
						close: function() {
							//$(this).dialog('close');
							$('#numbernotificationbox').remove();
					}
				});	
			}
		});
	});
	
	function validateEmpty(bodyName) {
		var isEmpty = false;
		$("#" + bodyName + "_tbody").find("tr").each(function() { 
			var row_id = $(this).attr("id");
			if($(this).find("input:eq(2)").val() != '') {
				$(this).find("input").each(function() { 
					if($(this).val() == '' && $(this).attr('id').search(/paid_/gi) == -1) {
						isEmpty = true;
						return false;
					}
				});
				if(isEmpty === true) { 
					$("tr[id='" + row_id + "']").effect("highlight", {color:'#CC3300'}, 18000);
					//$("tr[id='" + row_id + "']").css("background-color", "#CC3300");
					return false;
				}
			}
		});
		if(isEmpty == true) {
			return false;
		}
		return true;
	}
	
	if($("form[id='save_marketreport_reporting/fillreport_Form']").length > 0) {
		setInterval(function() { 				 
			if(sharedFunctions.checkSession() == false) {
				return;	
			}
			
			var id =  "save_marketreport_reporting/fillreport_Form".split("_");	
			var found_one = false;			
			
			if($("form[id='" + id[0] + "_" + id[1] + "_" + id[2] +"_Form']").find("textarea:enabled[value!='']").length > 0) {
				var formData = $("form[id='" + id[0] + "_" + id[1] + "_" + id[2] +"_Form']").serialize();
				sharedFunctions.requestAjax("post", "index.php?module=" + id[2] + "&action=save_" + id[1], formData, id[0] + "_" + id[1] + "_" + id[2] + "_Results", id[0] + "_" + id[1] + "_" + id[2] + "_Results");
			}
		}, 300000); // 300000 5 minutes
		
	}
	
	$("input[id^='save_'][id$='_Button']").click(function() {
		if(sharedFunctions.checkSession() == false) {
			return;	
		}
		var id =  $(this).attr("id").split("_");

		if(validateEmpty(id[1]) == true) {
			var formData = $("form[id='" + id[0] + "_" + id[1] + "_" + id[2] +"_Form']").serialize();

			formData = formData.replace(/[^=&]+=(&|$)/g,'');
			if(formData == '') {
				formData = "dummy=";	
			}
			
			var url = "index.php?module=" + id[2] + "&action=save_" + id[1];

			sharedFunctions.requestAjax("post", url, formData, id[0] + "_" + id[1] + "_" + id[2] + "_Results", id[0] + "_" + id[1] + "_" + id[2] + "_Results");
		}
	});
});