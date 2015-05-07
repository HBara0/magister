<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: products.php
 * Created:        @hussein.barakat    May 6, 2015 | 10:14:46 AM
 * Last Update:    @hussein.barakat    May 6, 2015 | 10:14:46 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if(!$core->input['action']) {
    if($core->input['pid'] && !empty($core->input['pid'])) {
        $pid = $db->escape_string($core->input['pid']);
        $prod_obj = new Products($pid, false);
        if(is_object($prod_obj) && !empty($prod_obj)) {
            $product = $prod_obj->get();
            $supplier_name = $prod_obj->get_supplier()->parse_link();
            if(isset($product['defaultFunction']) && !empty($product['defaultFunction'])) {
                $defaultfunc = $prod_obj->get_defaultchemfunction()->get_segapplicationfunction()->get_function()->get_displayname();
            }
            /* Get Chem Function Chem-START */
            $chemfuncprods = ChemFunctionProducts::get_data(array('pid' => $pid), array('returnarray' => true));
            if(is_array($chemfuncprods)) {
                foreach($chemfuncprods as $chemfuncprod) {
                    /* Get MI Data-START */
                    $marktetintel_objs = MarketIntelligence::get_marketdata_dal(array('cfpid' => $chemfuncprod->cfpid));
                    if(is_array($marktetintel_objs)) {
                        foreach($marktetintel_objs as $marktetintel_obj) {
                            $customer_ids[] = $marktetintel_obj->cid;
                            $entitybrandprod_objs = EntBrandsProducts::get_data(array('ebpid' => $marktetintel_obj->ebpid), array('returnarray' => true));
                            if(is_array($entitybrandprod_objs)) {
                                foreach($entitybrandprod_objs as $entitybrandprod_obj) {
                                    $ebids[] = $entitybrandprod_obj->ebid;
                                }
                            }
                        }
                    }
                    /* Get MI Data-END */
                    /* Get FunctionalProp-START */
                    $segapfunctions_objs = SegApplicationFunctions::get_data(array('safid' => $chemfuncprod->safid), array('returnarray' => true));
                    if(is_array($segapfunctions_objs)) {
                        foreach($segapfunctions_objs as $segapfunctions_obj) {
                            $segapfunct[$segapfunctions_obj->cfid] = $segapfunctions_obj->psaid;
                        }
                        /* Get FunctionalProp-END */
                    }
                }
            }

            /* Get Chem Function Chem-END */
            /* Get products and suppliers-START */
            $prodchemsubs_objs = ProductsChemicalSubstances::get_data(array('pid' => $pid), array('returnarray' => true));
            if(is_array($prodchemsubs_objs)) {
                foreach($prodchemsubs_objs as $prodchemsubs_obj) {
                    $chemical_ids[] = $prodchemsubs_obj->csid;
                }
            }
            /* Get products and suppliers-END */
            /* START PARSING */
            if(is_array($ebids)) {
                $itemscount['brandproducts'] = 0;
                $ebids = array_unique($ebids);
                foreach($ebids as $ebid) {
                    $entbrand_obj = new EntitiesBrands($ebid);
                    $brandproducts_rows.='<tr><td>'.$entbrand_obj->parse_link().'</td><td>'.$entbrand_obj->get_entity()->parse_link().'</td></tr>';
                    $itemscount['brandproducts'] ++;
                }
            }
            if(is_array($segapfunct)) {
                $itemscount['functprod'] = 0;
                foreach($segapfunct as $cfid => $psaid) {
                    $function_obj = new ChemicalFunctions($cfid, false);
                    $prodsegapp = new SegmentApplications($psaid, false);
                    $functprod_rows.='<tr><td>'.$function_obj->get_displayname().'</td><td>'.$prodsegapp->parse_link().'</td><td>'.$prodsegapp->get_segment()->parse_link().'</td></tr>';
                }
                $itemscount['functprod'] ++;
            }
            if(is_array($chemical_ids)) {
                $itemscount['chemsub'] = 0;
                $chemical_ids = array_unique($chemical_ids);
                foreach($chemical_ids as $chemical_id) {
                    $chemsub_obj = new Chemicalsubstances($chemical_id, false);
                    $chemsub_rows.='<tr><td>'.$chemsub_obj->parse_link().'</td><td>'.$chemsub_obj->casNum.'</td><td>'.$chemsub_obj->synonyms.'</td></tr>';
                    $itemscount['chemsub'] ++;
                }
            }
            if(is_array($customer_ids)) {
                $itemscount['customers'] = 0;
                $customer_ids = array_unique($customer_ids);
                foreach($customer_ids as $customer_id) {
                    $customer_obj = new Entities($customer_id, false);
                    $customers_rows.='<tr><td>'.$customer_obj->parse_link().'</td><td>'.$customer_obj->get_country()->get_displayname().'</td><td>'.$customer_obj->get_type().'</td></tr>';
                    $itemscount['customers'] ++;
                }
            }
            /* END PARSING */
        }
        eval("\$funcprop_list = \"".$template->get('profiles_products_functporplist')."\";");
        eval("\$customer_list = \"".$template->get('profiles_products_customerslist')."\";");
        eval("\$chemsub_list = \"".$template->get('profiles_products_chemicsubstlist')."\";");
        eval("\$brand_list = \"".$template->get('profiles_products_brandproductlist')."\";");
        eval("\$productprof = \"".$template->get('profiles_products')."\";");
        output_page($productprof);
    }
}