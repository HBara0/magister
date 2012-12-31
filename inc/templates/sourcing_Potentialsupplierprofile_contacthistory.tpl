
<div id="historybrief_{$contact_history[sschid]}"  onClick="$('#content_{$contact_history[sschid]}').toggle()" onMouseOver="$(this).toggleClass('mainmenuitem_hover')" >
    
            <a href="index.php?module=profiles/affiliateprofile&affid={$contact_history[affid]}" target="_blank">{$contact_history[affiliate]}</a>
            <a href="./users.php?action=profile&uid={$contact_history[uid]}">{$contact_history[displayName]}</a> 
            {$contact_history[date_output]}
             
  </div>
<div id="content_{$contact_history[sschid]}" style="display:none; padding:5px; margin-left: 15px; margin-right:15px;">

   
 {$reportcommunication_filled_section}

  </div>

