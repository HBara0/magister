<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listpotentialsupplier}</title>
        {$headerinc}
        <script lang="javascript">
            $(function() {
       
    
            });

        </script>
    </head>
        <body>
        {$header}
    <tr> {$menu}
        <td class="contentContainer"><h3>{$lang->listtrakers}</h3>
              <form action='$_SERVER[REQUEST_URI]' method="post">
                 <table class="datatable">
                <thead>
                    <tr>
                         <th style="width:20%">{$lang->deviceid} <a href="{$sort_url}&amp;sortby=deviceId&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=deviceId&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                        <th style="width:20%">{$lang->IMEI} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                        <th style="width:20%">{$lang->password} <a href="{$sort_url}&amp;sortby=password&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=password&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                        <th style="width:16%;">{$lang->phonenumber} <a href="{$sort_url}&amp;sortby=Phonenumber&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=Phonenumber&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                        <th style="width:20%;">{$lang->onasset} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a> </th>
                    
                    </tr>
                    {$filters_row}
                </thead>
                <tbody>
                         {$assets_trackerslistrow}
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
    
    </body>
   
</html>