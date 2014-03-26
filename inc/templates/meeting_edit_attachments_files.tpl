<tr  class="{$altrow}"id="file_{$meeting_attachments[mattid]}">
    <td>{$meeting_attachments[name]}</td>
    <td> <a id="deletefile_{$meeting_attachments[mattid]}_icon" href="#{$meeting_attachments[mattid]}">   <img src="{$core->settings[rootdir]}/images/invalid.gif"  alt="{$lang->deletefile}" border="0"> </a></td>
    <td><span id="deletecontainer_{$meeting_attachments[mattid]}"> </span> </td>
</tr>