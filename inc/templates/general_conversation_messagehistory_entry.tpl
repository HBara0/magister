<div style="width:100%;">
    <div class="{$bgcolor}" style="{$inline_style}margin-top:5px; position: relative; display: block; border: 1px #CFD5E2 solid; border-radius: 5px; padding:5px;">{$message[user][displayName]} (<span class="smalltext">{$message[message_date]}</span>):
        <div><p><em>{$message[message]}</em></p></div>
        <div style="position: absolute; right: 5px; bottom: 1px;{$show_replyicon}" class="hidden-print"><a href='#messagetoreply'><img src="{$core->settings[rootdir]}/images/icons/message_reply.png" id="replyto_{$message[scmid]}_{$message[viewPermission]}" title="{$lang->reply}"/></a></div>
    </div>
</div>