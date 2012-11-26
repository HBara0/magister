<tr id='{$contactp_rowid}'>
	<td>
		<input type='text' id='representative_{$contactp_rowid}_QSearch' autocomplete='off' size='40px'/>
		<input type='hidden' id='representative_{$contactp_rowid}_id' name='supplier[representative][id][]'/>
		<a href='#representative_{$contactp_rowid}_id' id='addnew_sourcing/managesupplier_representative'><img src='images/addnew.png' border='0' alt='{$lang->add}'></a>
		<div id='searchQuickResults_{$contactp_rowid}' class='searchQuickResults' style='display:none;'></div>
	</td>
</tr>