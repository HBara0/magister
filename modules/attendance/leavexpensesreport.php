<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: leavexpensesreport.php
 * Created:        @tony.assaad    Apr 7, 2014 | 2:46:03 PM
 * Last Update:    @tony.assaad    Apr 7, 2014 | 2:46:03 PM
 */




if(!($core->input['action'])) {
	if($core->input['referrer'] == 'generate') {
		$dimensionalize_ob = new DimentionalData();
		$expencesreport_data = ($core->input['expencesreport']);
		/* split the dimension and explode them into chuck of array */
		$expencesreport_data['dimension'] = $dimensionalize_ob->construct_dimensions($expencesreport_data['dimension']);

		$expences_indexes = array('expectedAmt', 'actualAmt');  
		$leave_expencesdata = Leaves::get_leaves_expencesdata($expencesreport_data['filter']);
		 	print_r($leave_expencesdata); 
		if(is_array($leave_expencesdata)) {
			

			$expencesreport_data['dimension'] = $dimensionalize_ob->set_dimensions($expencesreport_data['dimension']);
			$dimensionalize_ob->set_requiredfields($expences_indexes);
			$dimensionalize_ob->set_data($leave_expencesdata);
			
			$parsed_dimension = $dimensionalize_ob->get_output(array('outputtype' => 'table', 'noenclosingtags' => true));
			
//			 foreach($totals['gtotal'] as $report_header => $header_data) {
//			$dimension_head.= '<th>'.$report_header.'</th>';
//		 }
		}



		eval("\$expencesreport_output = \"".$template->get('attendance_expencesreport_output')."\";");
		output($expencesreport_output);
	}
}
?>