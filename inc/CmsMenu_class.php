<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * CMS News Class
 * $id: CmsMenu_class.php
 * Created:			@tony.assaad	September 12, 2012 | 01:53 PM
 * Last Update: 	@tony.assaad	September 12, 2012 | 2:55  PM
 */

class CmsMenu extends Cms {
    protected $status = 0;
    private $menu = array();
    private $menuitem = array();

    public function __construct($id = '', $simple = false) {
        if(isset($id) && !empty($id)) {
            $this->menu = $this->read($id, $simple);   /* Read the menu from the function and store the returned data in var menuitem */
        }
    }

    public function create($data, array $options = array()) {
        global $db, $log, $core, $errorhandler, $lang;
        $this->menu = $data;

        if(is_empty($this->menu['title'])) {
            $this->status = 1;
            return false;
        }

        if(value_exists('cms_menus', 'title', $this->menu['title'])) {
            $this->status = 2;
            return false;
        }

        $this->menu['createdBy'] = $core->user['uid'];
        $this->menu['dateCreated'] = TIME_NOW;
        $this->menu['title'] = $core->sanitize_inputs($this->menu['title'], array('removetags' => true));

        if(is_array($this->menu)) {
            $query = $db->insert_query('cms_menus', $this->menu);
            if($query) {
                $log->record($db->last_id());
            }
        }
    }

    public function create_menuitem($data) {
        global $db, $log, $core, $errorhandler, $lang;
        $this->menuitem = $data;

        if(value_exists('cms_menuitems', 'title', $this->menuitem['title'])) {
            $this->status = 2;
            return false;
        }

        $this->menuitem['createdBy'] = $core->user['uid'];
        $this->menuitem['dateCreated'] = TIME_NOW;
        $this->menuitem['title'] = $core->sanitize_inputs($this->menuitem['title'], array('removetags' => true));
        $this->menuitem['alias'] = parent::generate_alias($this->menuitem['alias']);
        $this->menuitem['publishedDesc'] = $core->sanitize_inputs($this->menuitem['publishedDesc'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true));


        /* validate configuration against set of defined configuratons */

        $accepted_configurations = array('webpages' => array('webpages' => 'webpages'), 'branchprofile' => array('branchprofile' => 'branchprofile'), 'affiliate' => array('affiliate'), 'externalurl' => array('link', 'linktitle', 'linkimage'));
        foreach($accepted_configurations as $key => $val) {
            if(!empty($this->menuitem['configurations'][$key])) {
                $this->menuitem['configurations'] = serialize($this->menuitem['configurations'][$key]);
                break;
            }
        } unset($this->menuitem['menuid'], $this->menuitem['itemid']);
        print_r($this->menuitem);

        if(is_array($this->menuitem)) {
            $query = $db->insert_query('cms_menuitems', $this->menuitem);
            if($query) {
                $log->record($db->last_id());
            }
        }
    }

    public function get_menus($options = '', $newsid = '') {
        global $db, $core;

        $sort_query = 'ORDER BY cm.title ASC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = 'ORDER BY '.$core->input['sortby'].' '.$core->input['order'];
        }

        if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
            $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
        }

        $limit_start = 0;
        if(isset($core->input['start'])) {
            $limit_start = $db->escape_string($core->input['start']);
        }

        if(isset($core->input['filterby'], $core->input['filtervalue'])) {
            $attributes_filter_options['title'] = array('title' => 'cp.');

            if($attributes_filter_options['title'][$core->input['filterby']] == 'int') {
                $filter_value = ' = "'.$db->escape_string($core->input['filtervalue']).'"';
            }
            else {
                $filter_value = ' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
            }

            $filter_where = ' WHERE '.$db->escape_string($attributes_filter_options['title'][$core->input['filterby']].$core->input['filterby']).$filter_value;
        }

        if(!empty($options) && !empty($newsid)) {
            $menu_query = "SELECT DISTINCT(cmi.cmsmiid), cmi.*
				FROM  ".Tprefix."cms_menuitems cmi
				JOIN ".Tprefix."cms_menus cm   ON(cmi.cmsmid=cm.cmsmid)
				WHERE cmi.cmsmid={$newsid}
				{$sort_query}
				LIMIT {$limit_start}, {$core->settings[itemsperlist]}";
        }
        else {
            $menu_query = "SELECT DISTINCT(cm.cmsmid), cm.*, u.displayname as creator
				FROM  ".Tprefix."cms_menus cm
				JOIN ".Tprefix."users u ON (u.uid=cm.createdBy)
				{$filter_where}
				{$sort_query}
				LIMIT {$limit_start}, {$core->settings[itemsperlist]}";
        }

        $menu_query = $db->query("{$menu_query}");

        if($db->num_rows($menu_query) > 0) {
            while($menu = $db->fetch_assoc($menu_query)) {
                if(!empty($options) && ($options == 'haschildren')) {
                    $menus[$menu['cmsmiid']] = $menu;
                }
                else {
                    $menus[$menu['cmsmid']] = $menu;
                }
            }
            return $menus;
        }
        return false;
    }

    public function get_status() {
        return $this->status;
    }

}
?>