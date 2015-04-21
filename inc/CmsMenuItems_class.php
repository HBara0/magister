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
                'configurations' => $data['configurations'],
                'metaDesc' => $data['metaDesc'],
                'metaKeywords' => $data['metaKeywords'],
                'robotsRule' => $data['robotsRule'],
                'sequence' => $data['sequence'],
                'type' => $data['type'],
                'createdBy' => $core->user['uid'],
                'dateCreated' => TIME_NOW,
        );
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
        $db->update_query('cms_menuitems', $menuitem_array, 'cmsmiid='.$menuitem_array['cmsmiid']);
    }

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

}