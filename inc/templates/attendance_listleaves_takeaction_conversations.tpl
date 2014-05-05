<div style="width:100%;">
    <div  class="{$bgcolor}" style="{$inline_style}margin-top:5px; display: block;  border: 1px #CFD5E2 solid; border-radius: 5px; padding:5px;">{$message[user][displayName]} :
        <p> {$message[lmid]} in reply to --{$message[inReplyTo]}
            <span style=" display:inline-block;"> {$message[message]}.</span>
            <span class="smalltext" style=" display:inline-block;"> {$message[message_dates]}.</span>
            <span style=" display:inline-block; padding:5px;"> <img src="{$core->settings[rootdir]}/images/icons/message_reply.png"  id="replyto_{$message[lmid]}_{$message[viewPermission]}"title="{$lang->reply}"/></span>
        </p>
    </div>

</div>
