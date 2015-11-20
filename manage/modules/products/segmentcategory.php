<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: segmentcategory.php
 * Created:        @hussein.barakat    Aug 10, 2015 | 1:24:41 PM
 * Last Update:    @hussein.barakat    Aug 10, 2015 | 1:24:41 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canManageSegments'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('products_segments');
if(!$core->input['action']) {
    eval("\$addsegmentcat = \"".$template->get('popup_admin_product_addsegmentcategory')."\";");
    $segmentcats = SegmentCategories::get_data();
    foreach($segmentcats as $segmentcat) {
        $segments_list .= "<tr><td>".$segmentcat->scid."</td><td>".$segmentcat->get_displayname()."</td><td>".$segmentcat->description."</td>";
        $segments_list .='<td><a style="cursor: pointer;" title="'.$lang->update.'" id="updatesegmentcatdtls_'.$segmentcat->scid.'_'.$core->input['module'].'_loadpopupbyid" rel="segmentcatdetail_'.$coid.'"><img src="'.$core->settings[rootdir].'/images/icons/update.png"/></a></td></tr>';
    }
    eval("\$segmentscatpage = \"".$template->get('admin_products_segmentcategory')."\";");
    output_page($segmentscatpage);
}
else {
    if($core->input['action'] == 'do_add_segmentcategory') {
        $segmentcat_obj = new SegmentCategories();
        $core->input['segmentcat']['name'] = generate_alias($core->input['segmentcat']['title']);
        $segmentcat_obj->set($core->input['segmentcat']);
        $segmentcat_obj = $segmentcat_obj->save();
        switch($segmentcat_obj->get_errorcode()) {
            case 0:
                if(is_array($core->input['lang'])) {
                    $trans_data['tableKey'] = intval($segmentcat_obj->{SegmentCategories::PRIMARY_KEY});
                    $trans_data['tableName'] = SegmentCategories::TABLE_NAME;
                    foreach($core->input['lang'] as $slid => $language) {
                        $trans_data['language'] = $slid;
                        if(is_array($language)) {
                            foreach($language as $field => $text) {
                                $trans_data['language'] = $slid;
                                $trans_data['field'] = $field;
                                $trans_data['text'] = $text;
                                $translation_obj = new Translations();
                                $translation_obj->set($trans_data);
                                $translation_obj->save();
                                if($translation_obj->get_errorcode() != 0) {
                                    output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                                    exit;
                                }
                            }
                        }
                    }
                }
                output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                exit;
            case 1:
                output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;
            case 2:
            default:
                output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
        }
        exit;
    }
    elseif($core->input['action'] == 'get_updatesegmentcatdtls') {
        $segmentcat_obj = new SegmentCategories($core->input['id'], false);
        $languages = SystemLanguages::get_data('slid !=1', array('returnarray' => true));
        if(is_array($languages)) {
            foreach($languages as $language) {
                $lid = $language->htmllang;
                $trans_fields = array('description', 'title', 'shortDescription', 'slogan');
                foreach($trans_fields as $field) {
                    $translation = Translations::get_translation($field, SegmentCategories::TABLE_NAME, $lid, $segmentcat_obj->{SegmentCategories::PRIMARY_KEY});
                    if(is_object($translation)) {
                        $trans[$lid][$field] = $translation->text;
                    }
                }
                eval("\$translationtabs_content=\"".$template->get('admin_products_translatesegmentcat_content')."\";");
                $lang_output .= $translationtabs_content;
                $lid = '';
            }
        }
        eval("\$translationsprodseg = \"".$template->get('admin_translationsprodfunc')."\";");
        $segmentcat = $segmentcat_obj->get();
        if($segmentcat_obj->publishOnWebsite == '1') {
            $publish_checked = 'checked="checked"';
        }
        if($segmentcat_obj->includeInWebsiteCarousel == '1') {
            $carousel_checked = 'checked="checked"';
        }
        eval("\$addsegmentcat = \"".$template->get('popup_admin_product_addsegmentcategory')."\";");
        output_page($addsegmentcat);
    }
}
?>