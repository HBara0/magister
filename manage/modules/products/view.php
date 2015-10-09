<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 *
 * View Products
 * $module: admin/products
 * $id: view.php
 * Last Update: @zaher.reda 	Apr 03, 2009 | 01:38 PM
 */
if(!defined('DIRECT_ACCESS')) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageProducts'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $sort_query = "p.name ASC";
    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $core->input['sortby']." ".$core->input['order'];
    }

    $sort_url = sort_url();
    $filters_config = array(
            'parse' => array('filters' => array('id', 'product', 'generic', 'segment', 'spid'),
                    'overwriteField' => array(
                            'id' => '',
                            'generic' => '',
                            'segment' => '',
                    )
            ),
            'process' => array(
                    'filterKey' => 'pid',
                    'mainTable' => array(
                            'name' => 'products',
                            'filters' => array('product' => array('operatorType' => 'equal', 'name' => 'pid'), 'spid' => array('operatorType' => 'equal', 'name' => 'spid')),
                    ),
                    'secTables' => array(
                            'productsegments' => array(
                                    'filters' => array('segment' => array('operatorType' => 'multiple', 'name' => 'psid'))
                            ),
                    )
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filter_where = null;
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));

    /* Perform inline filtering - END */
    $limit_start = 0;
    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }

    $query = $db->query("SELECT p.*, s.companyName AS supplier, g.title AS generic, ps.title AS segment
						FROM ".Tprefix."products p, ".Tprefix."entities s, ".Tprefix."genericproducts g, ".Tprefix."productsegments ps
						WHERE p.spid=s.eid AND g.gpid=p.gpid AND g.psid=ps.psid".$filter_where."
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");
    if($db->num_rows($query) > 0) {
        while($product = $db->fetch_array($query)) {
            $product_obj = new Products($product['pid']);
            $products_list .= "<tr class='{$class}'><td>{$product[pid]}</td><td><span class='editableinline' id='products/view_name_{$product[pid]}'>{$product_obj->parse_link()}</span></td><td>{$product[generic]}</td><td>{$product[segment]}</td><td><a href='".DOMAIN."/index.php?module=profiles/entityprofile&eid={$product[spid]}' target='_blank'>{$product[supplier]}</a></td>";
            $products_list .= "<td><a href='index.php?module=products/edit&amp;pid={$product[pid]}'><img src='{$core->settings[rootdir]}/images/edit.gif' alt='{$lang->edit}' border='0' /></a> <a href='#' id='mergeanddelete_{$product[pid]}_products/edit_icon'><img src='{$core->settings[rootdir]}/images/invalid.gif' border='0' /></a></td></tr>";
        }
        $multipages = new Multipages("products", $core->settings['itemsperlist']);
        $products_list .= "<tr><td colspan='5'>".$multipages->parse_multipages()."</td><td align='right'><a href='".$_SERVER['REQUEST_URI']."&amp;action=exportexcel'><img src='../images/xls.gif' alt='{$lang->exportexcel}' border='0' /></a></td></tr>";
    }
    else {
        $products_list = "<tr><td colspan='6' style='text-align: center;'>{$lang->noproductsavailable}</td></tr>";
    }

    $headerinc .= "<link href='{$core->settings[rootdir]}/css/jqueryuitheme/jquery-ui-1.7.2.custom.css' rel='stylesheet' type='text/css' />";

    eval("\$viewproductspage = \"".$template->get("admin_products_view")."\";");
    output_page($viewproductspage);
}
else {
    if($core->input['action'] == "exportexcel") {
        $sort_query = "p.name ASC";
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = $core->input['sortby']." ".$core->input['order'];
        }
        $query = $db->query("SELECT p.pid, p.name, g.title AS generic, ps.title AS segment, s.companyName AS supplier
						FROM ".Tprefix."products p, ".Tprefix."entities s, ".Tprefix."genericproducts g, ".Tprefix."productsegments ps
						WHERE p.spid=s.eid AND g.gpid=p.gpid AND g.psid=ps.psid
						ORDER BY {$sort_query}");
        if($db->num_rows($query) > 0) {
            $products[0]['pid'] = $lang->id;
            $products[0]['name'] = $lang->name;
            $products[0]['generic'] = $lang->generic;
            $products[0]['segment'] = $lang->segment;
            $products[0]['supplier'] = $lang->supplier;

            $i = 1;
            while($products[$i] = $db->fetch_assoc($query)) {
                $i++;
            }
            $excelfile = new Excel("array", $products);
        }
    }
}
?>