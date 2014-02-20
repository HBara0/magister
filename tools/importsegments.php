<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: importsegments.php
 * Created:        @tony.assaad    Feb 19, 2014 | 10:40:54 AM
 * Last Update:    @tony.assaad    Feb 19, 2014 | 10:40:54 AM
 */
// read csv file
require '../inc/init.php';
$core->input['delimiter'] = ';';


$csv_file = new CSV('Arranged segments test.csv', 1, true, $core->input['delimiter']);
$csv_file->readdata_file();
$data_header = $csv_file->get_header();
$data = $csv_file->get_data();
$headerval = implode('<br>', $data_header);


// define tables to be imported to
$tables = array(
		'productsegments' => array('pk' => 'psid', 'identifier' => 'title', 'headerval' => 'Segment', 'toinsert' => array('title' => 'Segment', 'titleAbbr')),
		'segmentapplications' => array('pk' => 'psaid', 'foreignid' => 'psid', 'identifier' => 'name', 'title' => 'title', 'headerval' => 'Application', 'toinsert' => array('psid', 'name', 'title' => 'Application', 'createdBy', 'createdOn')),
		'chemicalfunctions' => array('pk' => 'cfid', 'identifier' => 'name', 'title' => 'title', 'headerval' => 'Functional Property', 'toinsert' => array('name', 'title' => 'Functional Property', 'createdBy', 'createdOn')),
		//'segapplicationfunctions' => array('pk' => 'safid', 'toinsert' => array('cfid', 'psaid', 'createdBy', 'createdOn')),
);


if(is_array($tables)) {
	foreach($tables as $table => $table_config) {
		$temp_tables_fields[$table] = $db->show_fields_from($table);


		foreach($temp_tables_fields[$table] as $field) {
			$tables_fields[$table][$field['Field']] = $field['Field'];
		}
	}
	unset($temp_tables_fields);
}

if(is_array($data)) {
	foreach($data as $value) {
		foreach($tables as $table => $table_config) {
			echo'<hr>';
			//check if fiel name exist get  his id else insert it
			$where_segment = '';
			if($table == 'productsegments') {
				$oldpsid = $db->fetch_field($db->query("SELECT psid as psid from productsegments WHERE title='".$value[$table_config['headerval']]."'"), 'psid');
			}

			if($table == 'segmentapplications') {
				$where_segment = " ".$table_config['foreignid'].'!='.$oldpsid." ";
			}


			if(value_exists($table, $table_config['identifier'], $value[$table_config['headerval']])) {
				$tables_fields_values[$table][$table_config['localid']] = $db->fetch_field($db->query("SELECT ".$table_config['pk']." AS localid  FROM ".Tprefix."$table WHERE ".$table_config[identifier]." ='{$value[$table_config['headerval']]}'"), 'localid');
				// echo '  data exist take primary key  '.$tables_fields_values[$table][$table_config['localid']];
				$foreign_ids[$table_config['pk']] = $tables_fields_values[$table][$table_config['localid']];
			}
			else {  
				//check if segment application do not exist for this segmen to avoid duplications
				if(!value_exists($table, $table_config['identifier'], $value[$table_config['headerval']], $where_segment)) {
					foreach($tables[$table]['toinsert'] as $fieldkey => $fieldval) {
						switch($fieldval) {
							case 'name':
								$value[$fieldval] = $value[$table_config['headerval']];  //str_replace(' ', '', $value[$table_config['headerval']]);
								break;
							case 'createdOn':
								$value[$fieldval] = TIME_NOW;
								break;
							case 'createdBy':
								$value[$fieldval] = $core->user['uid'];
								break;
						}

						if(is_numeric($fieldkey)) {
							$fieldkey = $fieldval;
						}
						$datainsert[$fieldkey] = $value[$fieldval];
						print_r($datainsert);
						echo'<hr>';
					}

					//$db->insert_query($table, $datainsert);
					/*take the  foreing id (cfid,psaid upon first inser)*/
					$tables_fields_values[$table][$table_config['localid']] = $db->fetch_field($db->query("SELECT ".$table_config['pk']." AS localid  FROM ".Tprefix."$table WHERE ".$table_config[identifier]." ='{$value[$table_config['headerval']]}'"), 'localid');
					$foreign_ids[$table_config['pk']] = $tables_fields_values[$table][$table_config['localid']];
					unset($datainsert);
					//$value[$table_config['pk']] = $db->last_id();
				}
			}
			echo'<br>';

			$segarr = array('psaid' => $foreign_ids[psaid],
					'cfid' => $foreign_ids[cfid],
					'createdOn' => TIME_NOW,
					'createdBy' => $core->user['uid']
			);

			echo'<hr>';
			if(!empty($segarr['psaid']) && !empty($segarr['cfid'])) {

				if(!value_exists('segapplicationfunctions', 'cfid', $segarr['cfid'], ' psaid='.$segarr['psaid'])) {
					//echo ' insert into segapplicationfunctions psaid='.$segarr['psaid'].'cfid ='.$segarr[cfid];
					$db->insert_query('segapplicationfunctions', $segarr);
					unset($segarr);
				}
			}
			echo'<hr>';
		}
	}
}


//get the data
//do the checking
?>
