<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: manageaffdesc.php
 * Created:        @rasha.aboushakra    Jul 22, 2015 | 8:51:43 AM
 * Last Update:    @rasha.aboushakra    Jul 22, 2015 | 8:51:43 AM
 */

if($core->usergroup['canUseCms'] == 0) {
    error($lang->sectionnopermission);
    exit;
}
$lang->load('cms_meta');

if(!$core->input['action']) {
    $affid = intval($core->input['id']);
    if(empty($affid)) {
        redirect("index.php?module=cms/affdesclist");
    }
    $affiliate = Affiliates::get_affiliates(array('affid' => $affid), array('simple' => false));
    $languages = SystemLanguages::get_data('slid !=1', array('returnarray' => true));
    if(is_array($languages)) {
        foreach($languages as $language) {
            $lid = $language->slid;
            $trans_fields = array('description');
            foreach($trans_fields as $field) {
                $translation = Translations::get_translation($field, Affiliates::TABLE_NAME, $lid, $affiliate->{Affiliates::PRIMARY_KEY});
                if(is_object($translation)) {
                    $trans[$lid][$field] = $translation->text;
                }
            }
            eval("\$translationtabs_content=\"".$template->get('cms_affiliates_translationcontent')."\";");
            $lang_output .= $translationtabs_content;
            $lid = '';
        }
    }
    eval("\$translateaff =\"".$template->get('cms_manageaffdescription_translate')."\";");
    eval("\$manageaffdescription =\"".$template->get('cms_manageaffdescription')."\";");
    output_page($manageaffdescription);
}
elseif($core->input['action'] == 'do_perform_manageaffdesc') {
    $affiliate = new Affiliates($core->input['affid']);
    $affiliate_data = $affiliate->get();
    if(!empty($core->input['description'])) {
        $affiliate_data['description'] = $core->input['description'];
    }
    $query = $db->update_query('affiliates', $affiliate_data, 'affid = '.$affiliate->affid);
    if($query) {
        if(is_array($core->input['lang'])) {
            $trans_data['tableKey'] = intval($affiliate->{Affiliates::PRIMARY_KEY});
            $trans_data['tableName'] = Affiliates::TABLE_NAME;
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
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
            exit;
        }
        output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
    }
    else {
        output_xml('<status>false</status><message> Error while saving</message>');
    }
}