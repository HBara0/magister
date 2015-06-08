<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: CmsMenuItems_class.php
 * Created:        @hussein.barakat    Apr 21, 2015 | 10:36:53 AM
 * Last Update:    @hussein.barakat    Apr 21, 2015 | 10:36:53 AM
 */

class CmsMenuItems extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'cmsmiid';
    const TABLE_NAME = 'cms_menuitems';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'cmsmiid,cmsmid,title';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'alias';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        $menuitem_array = array(
                'cmsmid' => $data['cmsmid'],
                'title' => $data['title'],
                'alias' => $data['alias'],
                'parent' => $data['parent'],
                'itemclasses' => $data['itemclasses'],
                'isPublished' => $data['isPublished'],
                'datePublished' => $data['datePublished'],
                'publishedDesc' => $data['publishedDesc'],
                'lang' => $data['lang'],
                'type' => $data['type'],
                'metaDesc' => $data['metaDesc'],
                'metaKeywords' => $data['metaKeywords'],
                'robotsRule' => $data['robotsRule'],
                'sequence' => $data['sequence'],
                'type' => $data['type'],
                'createdBy' => $core->user['uid'],
                'dateCreated' => TIME_NOW,
        );

        /* validate configuration against set of defined configuratons */
        $accepted_configurations = array('webpage' => array('webpage' => 'webpage'), 'singlesegmentcategory' => array('singlesegmentcategory' => 'singlesegmentcategory'), 'singlesegment' => array('singlesegment' => 'singlesegment'), 'newsarchive' => array('newsarchive' => 'newsarchive'), 'eventsarchive' => array('eventsarchive' => 'eventsarchive'), 'listnews' => array('listnews' => 'listnews'), 'branchprofile' => array('branchprofile' => 'branchprofile'), 'affiliate' => array('affiliate'), 'externalurl' => array('link', 'linktitle', 'linkimage'), 'contact' => array('contact'));
        foreach($accepted_configurations as $key => $val) {
            if(empty($data['type'])) {
                break;
            }
            if(!empty($data['configurations'][$key])) {
                if(is_array($data['configurations'][$key])) {
                    if(!array_filter($data['configurations'][$key])) {
                        continue;
                    }
                }
                $menuitem_array['configurations'] = base64_encode(serialize(($data['configurations'][$key])));
            }
        }

        $query = $db->insert_query(self::TABLE_NAME, $menuitem_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $menuitem_array) {
        global $db, $core;
        $menuitem_array['modifiedBy'] = $core->user['uid'];
        $menuitem_array['dateModified'] = TIME_NOW;
        if(!isset($menuitem_array['cmsmiid'])) {
            $menuitem_array['cmsmiid'] = $this->cmsmiid;
        }

        /* validate configuration against set of defined configuratons */
        $accepted_configurations = array('webpage' => array('webpage' => 'webpage'), 'singlesegmentcategory' => array('singlesegmentcategory' => 'singlesegmentcategory'), 'singlesegment' => array('singlesegment' => 'singlesegment'), 'newsarchive' => array('newsarchive' => 'newsarchive'), 'eventsarchive' => array('eventsarchive' => 'eventsarchive'), 'listnews' => array('listnews' => 'listnews'), 'branchprofile' => array('branchprofile' => 'branchprofile'), 'affiliate' => array('affiliate'), 'externalurl' => array('link', 'linktitle', 'linkimage'), 'contact' => array('contact'));
        foreach($accepted_configurations as $key => $val) {
            if(empty($menuitem_array['type'])) {
                break;
            }
            if(!empty($menuitem_array['configurations'][$key])) {
                if(is_array($menuitem_array['configurations'][$key])) {
                    if(!array_filter($menuitem_array['configurations'][$key])) {
                        continue;
                    }
                }
                $menuitem_array['configurations'] = base64_encode(serialize(($menuitem_array['configurations'][$key])));
            }
        }
        $db->update_query('cms_menuitems', $menuitem_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
    }

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

    /**
     * recursive function
     * @global type $db
     * @param type $todelete
     * @return boolean
     */
    public function delete_menuitem($todelete) {
        global $db;
        $attributes = array('cmsmiid');
        foreach($attributes as $attribute) {
            $tables = $db->get_tables_havingcolumn($attribute, 'TABLE_NAME !="cms_menuitems"');
            if(is_array($tables)) {
                foreach($tables as $table) {
                    $query = $db->query("SELECT * FROM ".Tprefix.$table." WHERE ".$attribute."=".$todelete." ");
                    if($db->num_rows($query) > 0) {
                        $this->errorcode = 3;
                        return false;
                    }
                }
            }
        }
        $menuitems = CmsMenuItems::get_data(array('parent' => $todelete), array('returnarray' => true));
        if(is_array($menuitems)) {
            foreach($menuitems as $menuitem) {
                $menuitemtodelete = $menuitem->delete_menuitem($menuitem->cmsmiid);
                if(!$menuitemtodelete) {
                    return false;
                }
            }
        }
        $delete = $this->delete();
        if($delete) {
            $this->errorcode = 0;
            return true;
        }
    }

}