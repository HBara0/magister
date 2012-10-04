<?php
function calculate_stringdiff($old, $new) {
	if(!is_array($old)) {
		$old = explode(' ', $old);	
	}
	
	if(!is_array($new)) {
		$new = explode(' ', $new);	
	}
	$matrix = array();
	$maxlen = $omax = $nmax = 0;
	foreach($old as $okey => $oval) {
		$nkeys = array_keys($new, $oval);
	
		foreach($nkeys as $nkey) {
			if(isset($matrix[$okey-1][$nkey-1])) {
				$matrix[$okey][$nkey] = $matrix[$okey-1][$nkey-1] + 1;
			}
			else
			{
				$matrix[$okey][$nkey] = 1;
			}

			if($matrix[$okey][$nkey] > $maxlen){
				$maxlen = $matrix[$okey][$nkey];
				$omax = $okey + 1 - $maxlen;
				$nmax = $nkey + 1 - $maxlen;
			}	
		}
	}

	if($maxlen == 0) {
		return array(array('del' => $old, 'ins' => $new));
	}
	
	return array_merge(calculate_stringdiff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)), array_slice($new, $nmax, $maxlen), calculate_stringdiff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
}

function get_stringdiff($old, $new){
	$diff = calculate_stringdiff($old, $new);
	$stringdiff = '';
	foreach($diff as $val) {
		if(is_array($val)) {
			if(!empty($val['del'])) {
				$stringdiff .= '<strike>'.implode(' ', $val['del']).'</strike> ';
			}
			
			if(!empty($val['ins'])) {
				$stringdiff .= '<u>'.implode(' ', $val['ins']).'</u> ';
			}
		}
		else
		{
			$stringdiff .= $val.' ';
		}
	}
	return $stringdiff;
}
?>