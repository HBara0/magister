
<title>{$core->settings[systemtitle]} | {$lang->modifysitesettings}</title>
{$headerinc}
<link href="{$core->settings[rootdir]}/css/rateit.css" rel="stylesheet" type="text/css">
<style type="text/css">
span.listitem:hover { border-bottom: #CCCCCC solid thin; }
.blur {
   color: transparent;
   text-shadow: 0 0 5px rgba(0,0,0,0.5);
}
</style>
<script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
<script>
{$header_blurjs}

$(function(){

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
</head>
<body>
{$header}

<tr> 
    {$menu}
    <td class="contentContainer">
    <h3>{$potential_supplier_details[companyName]}  {$potential_supplier_details[rating]}</h3>

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
        {$sourcing_Potentialsupplierprofile_reportcommunication }


</div>

  </td>
</tr>
{$footer}

</body>
</html>
