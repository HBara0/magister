<tr id='{$chemicalrequest[scrid]}' class="{$rowcolor}">
	<td><a href="users.php?action=profile&amp;uid={$chemicalrequest[uid]}" target="_blank">{$chemicalrequest[displayName]}</a></td>
	<td>{$chemicalrequest[chemicalname]}</td>
	<td>{$chemicalrequest[requestDescription]}</td>
	<td>{$chemicalrequest[timeRequested_output]}</td>
	<td style="text-align:right;"><a href="#" rel="{$chemicalrequest[scrid]}" id="feedbackform_{$chemicalrequest[scrid]}_sourcing/listchemcialsrequests_loadpopupbyid"><img src="./images/{$feedback_icon}" border='0' alt="{$lang->feedback}" /></a></td>
</tr>