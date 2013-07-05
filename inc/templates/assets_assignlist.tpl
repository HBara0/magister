<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listpotentialsupplier}</title>
        {$headerinc}
        <script lang="javascript">
            $(function() {
       
            $('tr[id^="asset_"]').live('click',function() {
            var id = $(this).attr("id").split("_"); 
                $('tr').not('[id$='+id[1]+']').removeClass('highlight');
                $('tr[id$='+id[1]+']').toggleClass('highlight');
           
               $('a[rel^="delete_'+id[1]+'"]').css('display','block');
        });
        
        
            
            });

        </script>
    </head>

    <body>
        {$header}
    <tr> {$menu}
        <td class="contentContainer"><h3>{$lang->listassetusers}</h3>
            <table class="datatable">
                <thead>
                    <tr>

                        <th style="width:25%">{$lang->assignedusers}</th>
                        <th style="width:25%;">{$lang->assets} <a href="{$sort_url}&amp;sortby=asid&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=asid&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                        <th style="width:25%;">{$lang->fromdate} <a href="{$sort_url}&amp;sortby=fromDate&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=fromDate&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                        <th style="width:25%;">{$lang->todate} <a href="{$sort_url}&amp;sortby=toDate&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=toDate&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>

                    </tr>
                </thead>
                <tbody>
                    {$assignee_list}
                </tbody>

            </table>

        </td>

    </tr>
    {$footer}

</body>
</html>