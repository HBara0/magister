<tr class="{$rowclass}" id="asset_{$assigneduser[auid]}"> 
    <td><a href="./users.php?action=profile&amp;uid={$assigneduser[uid]}"  rel="{$assigneduser[asid]}" target="_blank">{$employee[displayName]}</a></td>
    <td>{$assigneduser[asset]}</td>
    <td>{$assigneduser[fromDate_output]}</td>
     <td>{$assigneduser[toDate_output]}</td>
     <td> <a href="#{$assigneduser[auid]}" style="display:none;" class="showpopup" id="showpopup_deleteassetuser" rel="delete_{$assigneduser[auid]}"><img src="http://10.0.0.98/ocos/images/invalid.gif" alt="{$lang->delete}" border="0"></a> </td>
     
</tr>


<div id="popup_deleteassetuser" title="{$lang->deleteuserassets}">
    <form id="perform_assets/listuser_Form" name="perform_assets/listuser_Form" action="#" method="post">
     <input type="hidden" name="action" value="perform_delete" />
    <input type="hidden" id="todelete" name="todelete" value="{$assigneduser[auid]}" />
    
    <p>{$lang->deleteuserassets}  <strong>{$employee[displayName]}</strong></p>
       <div align="center"><input type='button' id='perform_assets/listuser_Button' value='{$lang->yes}' class='button'/></div>
    </form>
</div>