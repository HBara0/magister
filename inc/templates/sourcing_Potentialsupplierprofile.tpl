<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->modifysitesettings}</title>
{$headerinc}
<link href="{$core->settings[rootdir]}/css/rateit.css" rel="stylesheet" type="text/css">
<link href="{$core->settings[rootdir]}/css/supplierprofile.css" rel="stylesheet" type="text/css">
<style type="text/css">
span.listitem:hover {
	border-bottom: #CCCCCC solid thin;
}
.blur {
	color: transparent;
	text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
}
</style>
<script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
<script>

$(function(){
	
	$("input[type='radio'][id=approved_type]").attr( 'disabled',true);
	$(".priceok").live('change',function() {
		var val= $(this).val();
		$(".approved,.notapproved").removeAttr( "disabled" );
		
		});
	$(".pricenotOk").live('change',function() {
		var val= $(this).val();
		$(".approved,.notapproved").attr( 'disabled', true);	
		});
		
	$(".approved").live('change',function() {
		/* find the first checkbox in the next parent div after each input with class approved*/
		var  obj = $(this).parent().parent().nextAll().has(":checkbox").first().find(":checkbox").removeAttr( "disabled" ).prop("checked",true); 
		$("div[id^='" + obj.val() + "']").show(); /* obj.val() Get the value of the checkbox in the next div (that has calss main) */
	
	});
	$(".stageapproved").live('change',function() {
		/* find the first checkbox in the next parent div after each radio checked with class stageapproved after the main Div*/
		var  obj = $(this).parent().parent().parent().nextAll().has(":checkbox").first().find(":checkbox").removeAttr( "disabled" ).prop("checked",true);
		$("div[id^='" + obj.val() + "']").show(); /* obj.val() Get the value of the checkbox in the next div (that has calss main) */
	});

	$(".stagenotapproved").live('change',function() {
		$(this).parent().parent().find("textarea,:text").attr("disabled",true);
		$("html, body").animate({ scrollTop: $(document).height() }, "slow");  /*scroll down to the end of body */
		$(this).parent().parent().parent().nextAll().has(":checkbox").first().find(":checkbox").attr( 'disabled', true);
		$("div[id^='sourcingnotpossible_body']").show();
		
	});
/*expand/collapse report section START*/

$("input[type='checkbox'][id$='_check']").live('change',function() {
			var id = $(this).attr("id");
				$("div[id^='" + $(this).val() + "']").slideToggle("slow");
		});		


$("span[id^='contactpersondata_']").each(function(){
	
	var rpid = $(this).attr('id').split('_');

	// We make use of the .each() loop to gain access to each element via the "this" keyword...
		$(this).qtip(
				{				
				content: {
				text: '<img class="throbber" src="images/loading.gif" alt="Loading..." />',
		 		
				ajax: {
					url: 'index.php?module=sourcing/supplierprofile&action=preview&rpid='+rpid[1],
				
					data: {},// Data to pass along with your request
						
					success: function(data,returnedData) {
			
					this.set( 'content.text', data);
					}
				},
				title: {
					text:  'Contact details', // Give the tooltip a title using each elements text
					button: true
					}
			},
			position: {
				viewport: $(window), // Keep the tooltip on-screen at all times
			},
			show: {
				event: 'mouseover',
				solo: true // Only show one tooltip at a time
			},
			hide: 'unfocus',
			style: {
				classes: ' ui-tooltip-light ui-tooltip-shadow'
			}
		})		
	})

	
});

</script>
</head><body>
{$header}
<tr> {$menu}
  <td class="contentContainer"><h3>{$supplier_details[companyName]}  {$supplier_details[rating]}</h3>
    <div id="suppliercontainer">
      <div style="display:table-cell; padding:10px;"></div>
      {$sourcing_Potentialsupplierprofile_contactsection}
      
      {$listcas_numbers_section}
      <div class="subtitle">{$lang->comments}</div>
      {$coBriefing_section}
      
      {$historical_section}
      
      {$sourcingRecords_section}
      
      {$marketingrecords_section}
      
      {$commentshare_section}
      <div class="subtitle">{$lang->contacthistory}</div>
      {$sourcing_Potentialsupplierprofile_contacthistory}
      {$sourcing_Potentialsupplierprofile_reportcommunication } </div></td>
</tr>
{$footer}
</body>
</html>