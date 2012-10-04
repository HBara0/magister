<?php
if(!defined("DIRECT_ACCESS")) {
	die('Direct initialization of this file is not allowed.');
} 
/* if($core->usergroup['grouppurchase_canViewOrdersList'] == 0)
{
	error($lang->sectionnopermission);
	exit;
}*/
if(!$core->input['action']) {

	eval("\$orderslist = \"".$template->get("grouppurchase_orderslist")."\";");
	output_page($orderslist);
}
else{ 
	if($core->input['action'] == 'do_change_orderslist') {
		//echo $headerinc;
		
		$query = $db->query("SELECT *,a.name as affname,p.name as pname FROM ".Tprefix."grouppurchase_orders o 
						JOIN ".Tprefix."products p ON (p.pid=o.pid)
						JOIN ".Tprefix."affiliates a ON (a.affid=o.affid)
						
								");//WHERE p.spid =".$db->escape_string($core->input['spid'])." AND o.ordered = 0
		
		if($db->num_rows($query) > 0) {
			
			while($orders = $db->fetch_array($query))  {
			
				$total = $orders['price'] * $orders['quantity'];
				
				//eval("\$orders_list .= \"".$template->get("grouppurchase_orderslist_row")."\";");
			/*  $orders_list .= "<tr><td><input type='checkbox' name='add[{$orders[oid]}]' id='add[{$orders[oid]}]' value='{$orders[oid]}'/></td><td>{$orders[pname]}</td><td>{$orders[affname]}</td><td>{$orders[price]}</td><td>{$orders[quantity]}</td><td>{$orders[currentStock]}</td><td>{$total}<input type='hidden' id='somme_{$orders[oid]}' name='somme_{$orders[oid]}' value ='{$total}' /></td></tr>"; */
			}
			$output = "<form  id='add_group purchase/orderslist_Form' name='add_group purchase/orderslist_Form' action='#' method='post'><table class='datatable' width='100%'><thead><tr><th width='2%'></th><th width='28%'>{$lang->product}</th><th width='30%'>{$lang->affiliate}</th><th width='10%'>{$lang->price}</th><th width='10%'>{$lang->quantity}</th><th width='10%'>{$lang->currentstock}</th><th width='10%'>{$lang->total}</th></tr></thead><tbody>{$orders_list}</tbody><tr><td colspan='5'></td><td class='subtitle' >{$lang->total}</td><td><input id='total' name='total' disabled='disabled' tabindex='2' size='9'></td></tr></table><input type='button' id='add_group purchase/orderslist_Button' value='{$lang->savecaps}'></form><div id='add_group purchase/orderslist_Results'></div>"; 
			//$output = $orders_list;
		}
		else
		{
			$output = $lang->nomatchfound;	
		}
		
		output_xml("<status>true</status><message><![CDATA[hello]]></message>"); 
	
		
		//output_xml($t);
		//output_page($output);
		//echo $output;
		/* ?>
			<script language="javascript" type="text/javascript">
			
				$(function() { 
					window.top.$("#change_group purchase/orderslist_Results").html("<?php echo addslashes($output); ?>");
				}); 
			</script>   
		<?php	 */
	}
	if($core->input['action'] == 'do_add_orderslist') {
	echo 'adding'; exit;
		 foreach($core->input['add'] as $oid) 
		{
			$query = $db->update_query('grouppurchase_orders', array('ordered' => '1'), "oid ='".$db->escape_string($oid)."'"); 	
			if($query) {
				$log->record($db->last_id());
			}
			else
			{
				$errors[] = $oid;
			}
			
		} 
		
		if(!isset($errors))		
		{
			output_xml("<status>true</status><message>{$lang->ordered}</message>");
		}
		else
		{ 	foreach($errors as $oid) 
			{
				$error .= $oid.'-';
			}
			output_xml("<status>false</status><message>{$lang->errorordering}{$error}</message>");
		}	
	}
}
?>