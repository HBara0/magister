<tr class="{$altrow_class}" id="markettimeline_{$mktintldata[mibdid]}"><td><div id="chemfuncproducts_{$mktintldata[cfpid]}">{$mktintldata[product]}</div><div style="display: none;">Timeline data history here</div></td>
    <td><div id="potential_{$mktintldata[mibdid]}">{$mktintldata[potential]} </div></td>
    <td><div id="mktSharePerc_{$mktintldata[mibdid]}"> {$mktintldata[mktSharePerc]} %</div> </td>
    <td>{$mktintldata[mktShareQty]}</td>
    <td><a  style="cursor: pointer;" title="{$lang->viewmrktbox}" id="mktintldetails_{$mktintldata[mibdid]}_profiles/entityprofile_loadpopupbyid" rel="mktdetail_{$mktintldata[mibdid]}"><img  src="{$core->settings[rootdir]}/images/icons/search.gif"/></a></td>
</tr>


