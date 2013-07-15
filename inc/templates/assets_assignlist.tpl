<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listpotentialsupplier}</title>
        {$headerinc}
        <script lang="javascript">
                   $(function() {
       
            $('tr[id^="asset_"]').live('mouseover',function() {
            var id = $(this).attr("id").split("_"); 
                $('tr').not('[id$='+id[1]+']').removeClass('highlight');
                $('a[id^=deleteuser_]').not('[rel^="delete_'+id[1]+'"]').css('display','none');
                $('a[id^=edituser_]').not('[rel^="edit_'+id[1]+'"]').css('display','none');
                $('tr[id$='+id[1]+']').toggleClass('highlight');
                $('a[rel^="delete_'+id[1]+'"]').css('display','block');
                $('a[rel^="edit_'+id[1]+'"]').css('display','block');
               
        });
    
 
            });
        </script>
    </head>

    <body>
        {$header}
    <tr> {$menu}
        <td class="contentContainer"><h3>{$lang->listassetusers}</h3>
            <form action='$_SERVER[REQUEST_URI]' method="post">
            <table class="datatable">
                <thead>
                    <tr>

                        <th style="width:25%">{$lang->assignedusers}</th>
                        <th style="width:25%;">{$lang->assets} <a href="{$sort_url}&amp;sortby=asid&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=asid&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                        <th style="width:25%;">{$lang->fromdate} <a href="{$sort_url}&amp;sortby=fromDate&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=fromDate&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                        <th style="width:25%;">{$lang->todate} <a href="{$sort_url}&amp;sortby=toDate&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=toDate&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>

                    </tr>
                    {$filters_row}
                </thead>
                <tbody>
                    {$assignee_list}
                </tbody>

            </table>
                </form>
<div style="width:40%; float:left; margin-top:0px;">
			<form method='post' action='$_SERVER[REQUEST_URI]'>
				{$lang->perlist}:
				<input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
			</form>
		</div>
        </td>

    </tr>
    	
    {$footer}

</body>
</html>