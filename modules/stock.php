<?php
$module['name'] = 'stock';
$module['title'] = $lang->stock;
$module['homepage'] = 'order';
$module['globalpermission'] = 'canUseStock';
$module['menu'] = array('file' 		  => array('order'),
						'title'		 => array('stockorder'),
						'permission'	=> array('stock_canOrderStock')
						);

?>