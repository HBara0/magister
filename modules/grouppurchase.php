<?php
$module['name'] = 'grouppurchase';
$module['title'] = $lang->grouppurchase;
$module['homepage'] = 'pricing';
$module['globalpermission'] = 'canUseGroupPurchase';
$module['menu'] = array('file' 		  => array('pricing', 'priceslist'),
						'title'		 => array('priceproduct', 'priceslist'),
						'permission'	=> array('grouppurchase_canPrice','canUseGroupPurchase')
						);
?>