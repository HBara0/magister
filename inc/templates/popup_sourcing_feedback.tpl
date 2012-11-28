<div id="popup_feedbackform"  title="{$lang->replyfeedback}" >
	<form name='perform_sourcing/listchemcialsrequests_Form' id='perform_sourcing/listchemcialsrequests_Form' method="post">
		<input type="hidden" id="action" name="action" value="do_feedback" />
		<input name="request[rid]" id="$chemicalrequest[scrid]" type="hidden" value="{$core->input[id]}">
		<div style="display:table; width:100%;">
			<div style="display:table-row">
				<div style="display:table-cell; width:100px; vertical-align:middle;">{$lang->feedback}</div>
				<div style="display:table-cell">
					<textarea name="feedback[feedback]" cols="40" rows="5">{$feedback[feedback]}</textarea>
				</div>
			</div>
			<div style="display:table-row">
				<div style="display:table-cell">{$lang->closefeedback}</div>
				<div style="display:table-cell">
					<input name="feedback[isClosed]" type="checkbox" value="1">
				</div>
			</div>
			<div style="display:table-row">
				<div style="display:table-cell">
					<hr />
					<input type="button" id="perform_sourcing/listchemcialsrequests_Button" class="button" value="{$lang->add}"/>
					<input type="reset" class="button" value="{$lang->reset}" />
				</div>
			</div>
		</div>
	</form>
	<div id="perform_sourcing/listchemcialsrequests_Results"></div>
</div>