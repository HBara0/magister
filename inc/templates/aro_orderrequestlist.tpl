<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->aroorderrequestlist}</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
        {$headerinc}
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>

    </head>
    <body>
        {$header}
    <tr> {$menu}
         <td class="contentContainer"><h1>{$lang->aroorderrequestlist}</h1>
            <div style="font-weight:bold;width:75%;float:right">{$lang->legend}</div>
            <div style="width:75%;float:right">
                <div style="display:inline-block;width:23%"> <div style="width:20px;display:inline-block" class="yellowbackground">-</div> Approval Process Started </div>
                <div style="display:inline-block;width:16%"> <div style="width:20px;display:inline-block" class="unapproved">-</div> No Approvals yet</div>
                <div style="display:inline-block;width:15%"> <div style="width:20px;display:inline-block" class="greenbackground">-</div> Fully Approved</div>
                <div style="display:inline-block;width:13%"><span class="glyphicon glyphicon-ban-circle" style="color:red"></span> ARO Rejected</div>
                <div style="display:inline-block;width:25%"> <span class="glyphicon glyphicon-exclamation-sign" ></span> ARO Approval pending at you</div>
                <br/><br/>
            </div>
            <form action='$_SERVER[REQUEST_URI]' method="post">
                <table class="datatable">
                    <thead>
                        <tr>
                            <th width="18%">{$lang->affiliate} <a href="{$sort_url}&amp;sortby=affid&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=affid&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="18%">{$lang->orderpurchasetype} <a href="{$sort_url}&amp;sortby=orderType&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=orderType&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="18%">{$lang->orderreference} <a href="{$sort_url}&amp;sortby=orderReference&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=orderReference&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="18%">{$lang->buyingcurr} <a href="{$sort_url}&amp;sortby=currency&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=currency&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                            <th width="18%">{$lang->createdon} <a href="{$sort_url}&amp;sortby=currency&amp;order=ASC"><img src="images/sort_asc.gif" border="0" /></a><a href="{$sort_url}&amp;sortby=currency&amp;order=DESC"><img src="images/sort_desc.gif" border="0" /></a></th>
                                    {$tools}
                            <th>&nbsp;</th>
                        </tr>
                        {$filters_row}
                    </thead>
                    <tbody class="datatable-striped">
                        {$aroorderrequest_rows}
                    </tbody>

                </table>
            </form>

        </td>
    </tr>
</body>
</html>