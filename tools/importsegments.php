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


$csv_file = new CSV('Arranged segments test2.csv', 1, true, $core->input['delimiter']);
$csv_file->readdata_file();
$data_header = $csv_file->get_header();
$data = $csv_file->get_data();
$headerval = implode('<br>', $data_header);


// define tables to be imported to
$tables = array(
		'productsegments' => array('pk' => 'psid', 'identifier' => 'title', 'checkattr' => 'Segment', 'headerval' => 'Segment', 'toinsert' => array('title' => 'Segment', 'titleAbbr')),
		'segmentapplications' => array('pk' => 'psaid', 'foreignid' => 'psid', 'identifier' => 'name', 'title' => 'title', 'checkattr' => 'Application', 'checkfor' => array('psid'), 'headerval' => 'Application', 'toinsert' => array('psid', 'name', 'title' => 'Application', 'createdBy', 'createdOn')),
		'chemicalfunctions' => array('pk' => 'cfid', 'identifier' => 'name', 'title' => 'title', 'checkattr' => 'Functional Property', 'headerval' => 'Functional Property', 'toinsert' => array('name', 'title' => 'Functional Property', 'createdBy', 'createdOn')),
		'segapplicationfunctions' => array('pk' => 'safid', 'identifier' => 'psaid', 'checkattr' => 'psaid', 'checkfor' => array('cfid'), 'toinsert' => array('cfid', 'psaid', 'createdBy', 'createdOn'))
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
	foreach($data as $rownum => $value) {
		echo '<br /><strong>Row '.$rownum.'</strong><br />';
		foreach($tables as $table => $table_config) {
			$found_item = null;
			echo '=> <em>Attempting to insert into '.$table.'</em><br />';
			$where_segment = '';
			if($table == 'productsegments') {
				$oldpsid = $db->fetch_field($db->query("SELECT psid as psid from productsegments WHERE title='".$value[$table_config['headerval']]."'"), 'psid');
			}

			if($table == 'segmentapplications') {
				$where_segment = " ".$table_config['foreignid'].'!='.$oldpsid." ";
			}

			$check_query_where = '';
			if(isset($table_config['checkfor']) && is_array($table_config['checkfor'])) {
				foreach($table_config['checkfor'] as $attr) {
					$check_query_where .= $attr.'="'.$value[$attr].'"';
				}
			}
			
			if(value_exists($table, $table_config['identifier'], $value[$table_config['checkattr']], $check_query_where)) {
				$found_item = true;
			}

			if($table_config['ignorecheck'] === true) {
				$found_item = false;
			}

			if($found_item == true) {
				$tables_fields_values[$table][$table_config['localid']] = $db->fetch_field($db->query("SELECT ".$table_config['pk']." AS localid  FROM ".Tprefix."$table WHERE ".$table_config[identifier]." ='{$value[$table_config['headerval']]}'"), 'localid');
				echo '==> '.$value[$table_config['headerval']].' already exists (ID='.$tables_fields_values[$table][$table_config['localid']].')<br />';
				$value[$table_config['pk']] = $tables_fields_values[$table][$table_config['localid']];
			}
			else {
				//check if segment application do not exist for this segmen to avoid duplications
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
				}

				$db->insert_query($table, $datainsert);
				$value[$table_config['pk']] = $db->last_id();
				echo '==> '.$value[$table_config['headerval']].' was inserted <br />';
				print_r($datainsert);
				echo '<br />';
	
				$tables_fields_values[$table][$table_config['localid']] = $db->fetch_field($db->query("SELECT ".$table_config['pk']." AS localid  FROM ".Tprefix."$table WHERE ".$table_config[identifier]." ='{$value[$table_config['headerval']]}'"), 'localid');
				$foreign_ids[$table_config['pk']] = $tables_fields_values[$table][$table_config['localid']];
				unset($datainsert);
			}

		}
	}
}

?>
