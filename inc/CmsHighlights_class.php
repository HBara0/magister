<?php
/* -------Definiton-START-------- */

class CmsHighlights extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'cmshid';
    const TABLE_NAME = 'cms_highlights';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'name';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'title';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        if(isset($data['title']) && !empty($data['title'])) {
            $data['name'] = generate_alias($data['title']);
        }
        else {
            $this->errorcode = 1;
            return $this;
        }
        if($data['type'] == 'html') {
            if(empty($data['inlineHtml'])) {
                $this->errorcode = 1;
                return $this;
            }
            unset($data['graph']);
        }
        else if($highlight['type'] == 'graph') {
            if(is_empty($data['graph']['imgPath'], $data['graph']['targetLink'], $data['graph']['graphTitle'], $data['graph']['description'])) {
                $this->errorcode = 1;
                return $this;
            }
            unset($data['inlineHtml']);
        }
        $table_array = array(
                'title' => $data['title'],
                'name' => $data['name'],
                'type' => $data['type'],
                'inlineHtml' => $data['inlineHtml'],
                'imgPath' => $data['graph']['imgPath'],
                'targetLink' => $data['graph']['targetLink'],
                'graphTitle' => $data['graph']['graphTitle'],
                'description' => $data['graph']['description'],
        );
        $table_array['isEnabled'] = 0;
        if($data['isEnabled'] == '1') {
            $table_array['isEnabled'] = 1;
        }
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            if(isset($data['title']) && !empty($data['title'])) {
                $update_array['name'] = generate_alias($data['title']);
            }
            else {
                $this->errorcode = 1;
                return $this;
            }
            if($data['type'] == 'html') {
                if(empty($data['inlineHtml'])) {
                    $this->errorcode = 1;
                    return $this;
                }
                unset($data['graph']);
            }
            else if($highlight['type'] == 'graph') {
                if(is_empty($data['graph']['imgPath'], $data['graph']['targetLink'], $data['graph']['graphTitle'], $data['graph']['description'])) {
                    $this->errorcode = 1;
                    return $this;
                }
                unset($data['inlineHtml']);
            }
            $update_array['isEnabled'] = 0;
            if($data['isEnabled'] == '1') {
                $update_array['isEnabled'] = 1;
            }
            $update_array['title'] = $data['title'];
            $update_array['type'] = $data['type'];
            $update_array['inlineHtml'] = $data['inlineHtml'];
            $update_array['imgPath'] = $data['graph']['imgPath'];
            $update_array['targetLink'] = $data['graph']['targetLink'];
            $update_array['graphTitle'] = $data['graph']['graphTitle'];
            $update_array['description'] = $data['graph']['description'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
}