<script>
    $(function () {
        $('img[id^="replyto_"]').bind('click', function () {
            var id = $(this).attr("id").split("_");
            $("#inreplyto").val(id[1]);
            var permission = id[2];
            $('input[id$="_' + permission + '"]').prop('checked', true);
            $('#message').focus();
        });
    });
</script>
{$lang->affiliate}: {$arorequest[affiliate]}<br />
{$lang->purchasetype}: {$arorequest[purchasetype]}<br />
{$lang->currency}:  {$arorequest[currency]}<br />
<hr />
<p><em>{$lang->approvearorequestnote}</em></p>
<form action="index.php?module=aro/managearodouments" method="post">
    <input type="hidden" name="action" value="approvearo" />
</form>
<hr />
<div class='subtitle'>{$lang->conversation}</div>
<form name="add_aro/managearodouments_Form" id="add_aro/managearodouments_Form" action="#" method="post">
    <input type="hidden" name="action" value="perform_sendmessage" />
    <input type="hidden" value="" id="inreplyto" name="arorequestmessage[inReplyTo]"/>
    <input type="hidden" id="messagerequestkey" name="messagerequestkey" value="{$core->input[requestKey]}" />
    <input type="hidden" value="{$core->input[id]}" id="inreplyto" name="aorid"/>
    <div id="messagetoreply" style="display:block; padding: 8px;"><textarea id="message" class="txteditadv" cols="40" rows="5" name="arorequestmessage[message]" placeholder='{$lang->writeyourmsghere}'></textarea>
        <div id="messagetoreply" style="display:none; padding:5px;">
            <span><input type="radio" id="permission_public" name="arorequestmessage[viewPermission]" title="{$lang->publictitle}" value="public" checked="checked">{$lang->public}</span>
            <span><input type="radio" disabled="disabled" id="permission_private" name="arorequestmessage[viewPermission]" title="{$lang->privatetitle}" value="private">{$lang->private}</span>
            <span><input type="radio" disabled="disabled" id="permission_limited" name="arorequestmessage[viewPermission]" title="{$lang->limitedtitle}" value="limited">{$lang->limited}</span>
        </div>
        <div><input type='button' id="add_aro/managearodouments_Button" value="&#x21b6; {$lang->reply}" class='button' /></div>
        <div id="add_aro/managearodouments_Results"></div>
        <div style="display:block;">{$takeactionpage_conversation}</div>
    </div>
</form>
