<head>
<title>{$core->settings[systemtitle]} | {$lang->fillsurvey}</title>
{$headerinc}
</head>
<body>
{$header}

<script>

$(function() {
	
		$("#searchlocation").live('click', function() { 
			if(sharedFunctions.checkSession() == false) {
				return;	
			}
			
			if(($("#altpickDateFrom").val() != '') && $("altpickDateTo").val() != '') {
				sharedFunctions.requestAjax("post", "index.php?module=assets/assetslocations&action=getlocations", "asid=" + $('#asset').val() +  "&view=" + $('#views').val() +"&fromDate=" + $("#altpickDateFrom").val()+$("#timelinefromTime").val() + "&toDate=" + $("#altpickDateTo").val()+$("#timelinetoTime").val(), 'locations_details', 'locations_details', true);
	
                            }
			else
			{			
				//sharedFunctions.requestAjax("post", "index.php?module=attendance/requestleave&action=getleavetime", "ltid=" + $('#type').val() + "&uid=" + $("#uid").val(), 'leavetime_details', 'leavetime_details', true);
			}
			
		});
	});


</script>

<tr>
{$menu}
    <td class="contentContainer">
        <h3>{$lang->titletimeline}</h3>
        <div  style="float:right;"><a href="{$change_view_url}"><img src="./images/icons/{$change_view_icon}" alt="{$lang->changeview}" border="0"/></a></div>
  
        <input type="hidden"  id="asset"  value="{$asid}"/>
        <input type="hidden"  id="views"  value="{$view}"/>
        <div style="display:inline-block; margin-right: 25px;margin-left: 28px;">{$lang->from}
            <div style="display:inline-block;"><input type="text" id="pickDateFrom"/> 
                <input type="hidden" id="altpickDateFrom"  name="timelineFrom"/>
                <input name="timelinefromTime" id="timelinefromTime"   size="8" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" placeholder="08:30" required="required" type="time"/>
            </div>
            
            
        </div>
        <div style="display:inline-block;">{$lang->to}
         <div style="display:inline-block;"><input type="text" id="pickDateTo"/> <input type="hidden" id="altpickDateTo"  name="timelineTo"/>
             <input name="timelinetoTime"  id="timelinetoTime"  size="8"pattern="(20|21|22|23|[01]\d|\d)(([:][0-17]\d){1,2})" placeholder="17:00" required="required" type="time"/>
            </div>
        </div>
 <div style="clear:left; padding:15px; margin-left:20px;"> 
     <input type="submit" class="button" style="cursor:pointer;" value="{$lang->search}" id="searchlocation" /></div>
 <hr>
 {$locations_grid}
 {$assets_map}
 <div id="locations_details" style="align:center; display:block;"></div>
    
    
    </td>
</tr>
{$footer}
</body>
</html>