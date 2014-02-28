<div style="display:block;" class="{$altrow}"> 
    <div style="display:inline-block;"><input name="event[invitee][]" type="checkbox"{$checked} value="{$user[uid]}"></div>
    <div style="display:inline-block; width: 45%;"><a href='./users.php?action=profile&uid={$user[uid]}' target='_blank'>{$user[displayName]}</a></div>
    <div style="display:inline-block; width: 45%;">{$user[affiliate]}</div>
</div>