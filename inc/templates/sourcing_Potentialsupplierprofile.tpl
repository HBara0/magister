<html>
<head>
<title>{$core->settings[systemtitle]} | {$supplier[maindetails][companyName]}</title>
{$headerinc}
<link href="{$core->settings[rootdir]}/css/rateit.css" rel="stylesheet" type="text/css" />
<link href="{$core->settings[rootdir]}/css/supplierprofile.css" rel="stylesheet" type="text/css" />
<link href="{$core->settings[rootdir]}/css/rml.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.blur {
	color: transparent;
	text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
}
</style>
<script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
<script>
$(function(){
	{$header_blurjs}
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
	
	var id = $(this).attr('id').split('_');

	// We make use of the .each() loop to gain access to each element via the "this" keyword...
		$(this).qtip(
				{				
				content: {
				text: '<img class="throbber" src="images/loading.gif" alt="Loading..." />',
		 		
				ajax: {
					url: 'index.php?module=sourcing/supplierprofile&action=preview&sid='+id[2]+'&rpid='+id[1],
				
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
		});		
	});
});
</script>
</head><body>
{$header}
<tr> {$menu}
	<td class="contentContainer">
		<div>
			<h3>{$supplier[maindetails][companyName]} {$supplier[maindetails][businessPotential_output]}</h3>
			{$supplier[maindetails][relationMaturity_output]}
		</div>
		<div style='display:inline-block; width:50%; padding:5px; vertical-align:top;'>
			<div class="subtitle border_right"><strong>{$lang->contactdtails}</strong></div>
			<div class="border_right">{$lang->fulladress}: <span class="contactsvalue">{$supplier[contactdetails][fulladress]}</span><br />
                {$lang->country}: <span class="contactsvalue">{$supplier[contactdetails][country]}</span><br />
                {$lang->city}: <span class="contactsvalue">{$supplier[contactdetails][city]}</span><br />
                {$lang->postcode}: <span class="contactsvalue">{$supplier[contactdetails][postCode]}</span><br />
            	{$lang->pobox}: <span class="contactsvalue">{$supplier[contactdetails][poBox]}</span><br />
				{$lang->telephone}: <span class="contactsvalue">{$supplier[contactdetails][phones]}</span><br />
				{$lang->fax}: <span class="contactsvalue">{$supplier[contactdetails][fax]}</span><br />
				{$lang->email}: <span class="contactsvalue">{$supplier[contactdetails][mainEmail]}</span><br />
				{$lang->website}: <span class="contactsvalue">{$supplier[contactdetails][website]}</span><br />
			</div>
			<div class="border_right">{$contactsupplier_button}</div>
		</div>
		<div style='display:inline-block; width:45%; padding:5px; vertical-align:top;'>
			<div class="subtitle"><strong>{$lang->contactperson}</strong></div>
			{$contactpersons_output}</div>
		<div style='display:inline-block; width:50%; padding:5px; margin-top:10px; vertical-align:top;' class="border_right"><strong>{$lang->segments}</strong><br />
			{$segments_output}</div>
		<div style='display:inline-block; width:45%; padding:5px; margin-top:10px; vertical-align:top;'><strong>{$lang->activityarea}</strong><br />
			{$activityarea_output}</div>
		<div style="width:100%; max-height: 200px; overflow:auto; display:inline-block; vertical-align:top; margin-top: 10px;">
			<table class="datatable" width="100%">
				<thead>
					<tr>
						<td class="thead">{$lang->casnum}</td>
						<td class="thead">{$lang->checmicalproduct}</td>
						<td class="thead">{$lang->supplytype}</td>
						<td class="thead">{$lang->synonyms}</td>
					</tr>
				</thead>
				{$chemicalslist_section}
			</table>
			<hr /> 
		</div>
		<div>
			<div class="subtitle" style="margin-top: 10px;">{$lang->comments}</div>
			<div style='padding:5px; width:100%;' class='border_bottom'><strong>{$lang->cobriefing}</strong><br />
				<p>{$supplier[maindetails][coBriefing]}</p></div>
			<div style='padding:5px; width:100%;' class='border_bottom'><strong>{$lang->historical}</strong><br />
				<p>{$supplier[maindetails][historical]}</p></div>
			<div style='display:inline-block; width:45%; padding:5px; vertical-align:top;' class='border_bottom border_right'><strong>{$lang->marketingrecords}</strong><br />
				<p>{$supplier[maindetails][marketingRecords]}</p></div>
			<div style='display:inline-block; width:45%; padding:5px;' class='border_bottom'><strong>{$lang->sourcingrecords}</strong><br />
				<p>{$supplier[maindetails][sourcingRecords]}</p></div>
			<div style='padding:5px;' class='border_bottom'><strong>{$lang->commentstoshare}</strong><br />
				<p>{$supplier[maindetails][commentsToShare]}</p></div>
			<div>
				<hr />
				<div class="subtitle">{$lang->contacthistory}</div>
				{$contacthistory_section}
				{$reportcommunication_section} </div>
		</div></td>
</tr>
{$footer}
</body>
</html>