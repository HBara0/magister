<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->sendreportsto} {$suppliername}</title>
{$headerinc}
</head>
<body>
{$header}
<tr>
{$menu}
    <td class="contentContainer">
    <h3>{$lang->sendreportsto} {$suppliername}</h3>
    <form id="sendbymail_Form" name="sendbymail_Form" method="post" action="index.php?module=reporting/sendbymail&amp;action=do_sendbymail&amp;identifier={$core->input[identifier]}">
    <fieldset class="altrow">
    	<legend><strong>{$lang->recipients}:</strong></legend>
        <table width="100%" cellpadding="0" cellspacing="0">
    	{$representatives_list}
        </table>
        <hr />
        {$lang->additionalrecipients}: <input type="text" size="50" name="additional_recipients" id="additional_recipients" value="{$default_cc}"/> <span class="smalltext">{$lang->seperatedbycomma}.</span>
    </fieldset>
    <hr />
    <p>
    <strong>{$lang->subject}:</strong> <input type="text" size="100" maxlength="70" name="subject" id="subject" value="{$lang->sendbymailsubject}"/><br />
    <strong>{$lang->message}:</strong><br />
    <textarea class="texteditormin" cols="100" rows="20" name="message" id="message">{$lang->sendbymaildefault}</textarea>
    </p>
    <p>
    <fieldset class="altrow">
        <legend><strong>{$lang->attachments}:</strong></legend>
        <ul>
        	{$attachments}
        </ul>
    </fieldset>
    </p>
    <p><hr /></p>
    <input type="submit" value="{$lang->send}" id="sendbymail" class="button"/>
    </form>
    </td>
</tr>
{$footer}
</body>
</html>