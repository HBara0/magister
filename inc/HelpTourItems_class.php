<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: HelpTourItems_class.php
 * Created:        @rasha.aboushakra    Oct 5, 2015 | 4:45:26 PM
 * Last Update:    @rasha.aboushakra    Oct 5, 2015 | 4:45:26 PM
 */

/**
 * Description of HelpTourItems_class
 *
 * @author rasha.aboushakra
 */
class HelpTourItems extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'htiid';
    const TABLE_NAME = 'helptouritems';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

    public function get_helptouritems($reference) {
        global $lang;
        $items = HelpTourItems::get_data(array('reference' => $reference), array('returnarray' => true, 'order' => array('by' => 'sequence', 'sort' => 'ASC')));
        if(is_array($items)) {
            foreach($items as $item) {
                $langVar = $item->langVar;
                $text = $item->text;
                if((isset($langVar) && !empty($langVar)) && !empty($lang->$langVar)) {
                    $text = $lang->$langVar;
                }
                if(!empty($text)) {
                    $touritems[$item->itemId] = array('ignoreId' => $item->ignoreId, 'options' => $item->options, 'text' => $text);
                }
            }
        }
        return $touritems;
    }

}