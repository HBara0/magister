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

    public function update($data, array $options = array()) {
        global $db, $log, $core, $errorhandler, $lang;
        $this->menu = $data;

        if(is_empty($this->menu['title'])) {
            $this->status = 1;
            return false;
        }

        if(!value_exists('cms_menus', 'title', $this->menu['title'])) {
            $this->status = 3;
            return false;
        }

        $this->menu['modifiedBy'] = $core->user['uid'];
        $this->menu['modifiedOn'] = TIME_NOW;
        $this->menu['title'] = $core->sanitize_inputs($this->menu['title'], array('removetags' => true));

        if(is_array($this->menu)) {
            $query = $db->update_query(Tprefix.'cms_menus', $this->menu, 'cmsmid ='.intval($this->menu['cmsmid']));
            if($query) {
                $log->record($db->last_id());
            }
        }
    }

    public function create_menuitem($data) {
        global $db, $log, $core, $errorhandler, $lang;
        $this->menuitem = $data;


        if(is_empty($this->menuitem['title'], $this->menuitem['alias'])) {
            $this->status = 1;
            return false;
        }

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

        $accepted_configurations = array('webpage' => array('webpage' => 'webpage'), 'singlesegment' => array('singlesegment' => 'singlesegment'), 'newsarchive' => array('newsarchive' => 'newsarchive'), 'eventsarchive' => array('eventsarchive' => 'eventsarchive'), 'listnews' => array('listnews' => 'listnews'), 'branchprofile' => array('branchprofile' => 'branchprofile'), 'affiliate' => array('affiliate'), 'externalurl' => array('link', 'linktitle', 'linkimage'), 'contact' => array('contact'));
        $configs = $this->menuitem['configurations'];

        foreach($accepted_configurations as $key => $val) {
            if(!empty($this->menuitem['configurations'][$key])) {
                $this->menuitem['configurations'] = base64_encode(serialize(($this->menuitem['configurations'][$key])));
            }
        }
        unset($this->menuitem['menuid'], $this->menuitem['itemid']);
        if(is_array($this->menuitem)) {
            $query = $db->insert_query('cms_menuitems', $this->menuitem);
            if($query) {
                $log->record($db->last_id());
                $this->status = 0;
                return true;
            }
        }
    }

    public function get_menus($options = '', $newsid = '') {
        global $db, $core;

        $sort_query = 'ORDER BY cm.title ASC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = 'ORDER BY '.$core->input['sortby'].' '.$core->input['order'];
        }

        if(isset($core->input ['perpage']) && !empty($core->input['perpage'])) {
            $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
        }

        $limit_start = 0;
        if(isset($core->input['start'])) {
            $limit_start = $db->escape_string($core->input['start']);
        }

        if(isset($core->input['filterby'], $core->input['filtervalue'])) {
            $attributes_filter_options['title'] = array('title' => 'cp.');

            if($attributes_filter_options['title'][$core->input ['filterby']] == 'int') {
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
        return
                false;
    }

    public function get_status() {
        return $this->status;
    }

    public function read_menu_children($id, $simple = false) {
        global $db;

        $query_select = '*';
        if($simple == true) {
            $query_select = 'cmsmiid, parent,sequence';
        }

        $query = $db->query("SELECT {$query_select} FROM ".Tprefix."cms_menuitems WHERE parent=".$db->escape_string($id).' ORDER BY sequence ASC,title ASC');
        if($db->num_rows($query) > 0) {
            while($menu = $db->fetch_assoc($query)) {
                $menus[$menu['cmsmiid']] = $menu;
                $menus[$menu['cmsmiid']]['children'] = $this->read_menu_children($menu['cmsmiid'], $simple);
            }
            return $menus;
        }

        return false;
    }

    public function read_menus($menuid, $simple = false) {
        global $db, $core;

        $query_select = '*';
        if($simple == true) {
            $query_select = 'cmsmiid, parent,sequence';
        }

        $query = $db->query("SELECT {$query_select} FROM ".Tprefix."cms_menuitems WHERE cmsmid=".$menuid." AND parent=0 ORDER BY sequence ASC");
        if($db->num_rows($query)) {
            while($menu = $db->fetch_assoc($query)) {
                $level = 'parent';
                if($menu['parent'] != 0) {
                    $level = 'children';
                }

                $menus[$menu['cmsmiid']] = $menu;
                $menus[$menu['cmsmiid']]['children'] = $this->read_menu_children($menu['cmsmiid'], $simple);
            }

            return $menus;
        }
        return false;
    }

    public function parse_menu_list(array $menus = array(), $highlevel = true, $parsetype = 'list', $config = array()) {
        global $core;
        if(empty($menus)) {
            if(!isset($this->menu)) {
                return false;
            }

            if($highlevel == true) {
                $menus = $this->menu;
            }
            else {
                return false;
            }
        }

        if($highlevel == true) {
            if($parsetype == 'list') {
                $menus_list = '<ul>';
            }
            else {
                $menus_list = '<select name="'.$config['name'].'" id="'.$config['id'].'">';
            }
        }

        foreach($menus as $id => $values) {

            if($parsetype == 'list') {
                $editlink = '&nbsp;&nbsp;<a href="'.$core->settings['rootdir'].'/index.php?module=cms/managemenu&mitid='.$values['cmsmiid'].'" target="_blank"><img src="'.$core->settings['rootdir'].'/images/edit.gif"></a>';
                $editlink .='<a href="index.php?module=cms/managemenu&type=addmenuitem&id='.$values['cmsmid'].'&parent='.$values['cmsmiid'].'" target="_blank"  title="'.$lang->addmenuitem.'"><img src="'.$core->settings['rootdir'].'/images/addnew.png" border="0"/></a>';
                $editlink .='<a href="#'.$values['cmsmiid'].'" id="deletemenuitem_'.$values['cmsmiid'].'_cms/listmenu_loadpopupbyid" title="'.$lang->deletemenuitem.'"><img src="'.$core->settings['rootdir'].'/images/invalid.gif" border="0"/></a>';
                $menus_list .= '<li>';
                if($values['sequence'] != '0' || !empty($values['sequence'])) {
                    $sequence = $values['sequence'].'- ';
                }
                $menuitem_name = '<div style = "width: 80%;display:inline-block;">'.$sequence.$values['title'].$editlink;
                if(is_array($values['children']) && !empty($values['children'])) {
                    $menus_list .= ' <a style="font-weight:bold" href = "#menu_'.$values['cmsmiid'].'" id = "showmore_menuchildren_'.$values['cmsmiid'].'">'.$menuitem_name.'
                </a>';
                }
                else {
                    $menus_list .= $menuitem_name;
                }
                $menus_list .= '</div>';
                if($values['children'] == 0) {
                    $menus_list .= '<div style = "width: 10%; display:inline-block;">0</div>';
                }
                else {
                    $menus_list .= '<div style = "width: 10%; display:inline-block;">'.count($values['children']).'</div>';
                }
//                $menus_list .= '<div style = "width: 10%; display:inline-block; text-align: right;">'.$editlink.'</div>';
                $menus_list .= '</li>';
                $menus_list .='<hr>';
            }
            else {
                $menus_list .= '<option value = "'.$values['cmsmiid'].'"> '.$values['title'].'</option>';
            }

            if(is_array($values['children']) && !empty($values['children'])) {
                if($parsetype == 'list') {
                    $menus_list .= '<ul id = "menuchildren_'.$values['cmsmiid'].'" style = "display:none;">';
                    $menus_list .= $this->parse_menu_list($values['children'], false);
                    $menus_list .= '</ul>';
                }
                else {
                    $menus_list .= $this->parse_menu_list($values['children'], false, 'select');
                }
            }
        }

        if($highlevel == true) {
            if($parsetype == 'list') {
                $menus_list .= '</ul>';
            }
            else {
                $menus_list .= '</select>';
            }
        }


        return $menus_list;
    }

    public function get() {
        return $this->menu;
    }

    protected function read($id, $simple = false) {
        global $db;

        if(empty($id)) {
            return false;
        }

        return $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."cms_menus WHERE cmsmid=".$db->escape_string($id)));
    }

}
?>