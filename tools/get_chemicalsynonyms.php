<?php
/*
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: get_chemicalsynonyms.php
 * Created:        @zaher.reda    Nov 29, 2012 | 3:20:07 PM
 * Last Update:    @zaher.reda    Nov 29, 2012 | 3:20:07 PM
 */

require '../inc/init.php';

//$syns = array();
//$query = $db->query('SELECT * FROM '.Tprefix.'chemicalsubstances WHERE synonyms IS NOT NULL');
//while($substance = $db->fetch_assoc($query)) {
//	$syn = explode(';', $substance['synonyms']);
//	$syns = array_merge($syns, $syn);
//}
//$syns = array_unique($syns);
//echo count($syns);
//exit;
$lang->settings['charset'] = 'UTF-8';
$query = $db->query('SELECT * FROM '.Tprefix.'chemicalsubstances WHERE (synonyms IS NULL) OR (synonyms IS NULL AND synonyms!="n/a") ORDER BY RAND() LIMIT 0, 20');
while($substance = $db->fetch_assoc($query)) {
	$raw_data = file_get_contents('http://www.ncbi.nlm.nih.gov/pcsubstance?term="'.$substance['casNum'].'"[Synonym]&presentation=uilist');
	
	if(strstr($raw_data, 'No items found')) {
		$db->update_query('chemicalsubstances', array('synonyms' => 'n/a'), 'csid='.$substance['csid']);
		continue;
	}
	
	$raw_data = $core->sanitize_inputs($raw_data, array('removetags' => true));

	$substance['sids'] = array_filter(explode("\n", $raw_data));
	$substance['synonyms'] = array();
	
	foreach($substance['sids'] as $sid) {
		$sid = trim($sid);
		if(empty($sid) || strlen($sid) < 1) {
			continue;
		}
		$data = file_get_contents('http://pubchem.ncbi.nlm.nih.gov/summary/summary.cgi?q=nmcv&namedisopt=&sid='.$sid);
		
		$csv = new CSV($data, 2, true);	
		$csv->readdata_string(array('Synonyms'));

		foreach($csv->get_data() as $entry) {
			if($entry['Synonyms'] == $substance['casNum'] || strtolower($entry['Synonyms']) == $substance['name']) {
				continue;
			}
			$substance['synonyms'][] = $entry['Synonyms'];
		}
	}
	$substance['synonyms'] = array_unique($substance['synonyms']);
	$update_query = $db->update_query('chemicalsubstances', array('synonyms' => implode('; ', $substance['synonyms'])), 'csid='.$substance['csid']);
	if($update_query) {
		echo 'Updated '.$substance['name'].' '.$substance['cadNum'].'<br />';
	}
	else
	{
		echo 'FAILD to update '.$substance['name'].' '.$substance['cadNum'].'<br />';
	}
	unset($substance['synonyms']);
}
?>