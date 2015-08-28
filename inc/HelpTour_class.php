<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: HelpTour_class.php
 * Created:        @zaher.reda    Aug 28, 2015 | 9:15:46 AM
 * Last Update:    @zaher.reda    Aug 28, 2015 | 9:15:46 AM
 */

/**
 * Description of HelpTour_class
 *
 * @author zaher.reda
 */
class HelpTour {
    private $id = 'default_helptour';
    private $cookiename = 'default_helptour';
    private $items = array();

    public function __construct() {

    }

    public function set_id($id) {
        if(empty($id)) {
            return;
        }
        $this->id = $id;
    }

    public function set_cookiename($name) {
        if(empty($name)) {
            return;
        }
        $this->cookiename = $name;
    }

    public function set_items(array $items) {
        if(empty($items)) {
            return;
        }
        $this->items = $items;
    }

    public function add_item($id, array $options) {
        if(!isset($options['text']) && !isset($options['langvar'])) {
            return;
        }
        $this->items[$id] = $options;
    }

    private function parse_items() {
        $output = '';

        end($this->items);
        $lastid = key($this->items);
        reset($this->items);
        foreach($this->items as $id => $item) {
            if(isset($item['text'])) {
                $text = $item['text'];
            }

            if(isset($item['langvar'])) {
                $text = $lang->{$item['langvar']};
            }

            if($id == $lastid) {
                $lastbutton = ' data-button="'.$lang->close.'Close"';
            }
            $output .= '<li data-id="'.$id.'"'.$lastbutton.'><p>'.$text.'</p></li>';
        }
        return $output;
    }

    public function parse($options = array()) {
        global $template, $headerinc, $core;

        if($options['skip_headerinc'] != true) {
            eval("\$headerinc .= \"".$template->get('headerinc_helptour')."\";");
        }
        $items = $this->parse_items();
        eval("\$help_helptour = \"".$template->get('help_helptour')."\";");
        return $help_helptour;
    }

}