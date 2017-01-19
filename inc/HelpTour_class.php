<?php

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
        if (empty($id)) {
            return;
        }
        $this->id = $id;
    }

    public function set_cookiename($name) {
        if (empty($name)) {
            return;
        }
        $this->cookiename = $name;
    }

    public function set_items(array $items) {
        if (empty($items)) {
            return;
        }
        $this->items = $items;
    }

    public function add_item($id, array $options) {
        if (!isset($options['text']) && !isset($options['langvar'])) {
            return;
        }
        $this->items[$id] = $options;
    }

    private function parse_items() {
        global $core;
        $output = '';

        end($this->items);
        $lastid = key($this->items);
        reset($this->items);
        foreach ($this->items as $id => $item) {
            if (isset($item['text'])) {
                $text = $item['text'];
            }

            if (isset($item['langvar'])) {
                $text = $lang->{$item['langvar']};
            }

            if ($id == $lastid) {
                $attrs['data-button'] = ' data-button="' . $lang->close . 'Close"';
            }

            if (isset($item['options']) && !empty($item['options'])) {
                $attrs['data-options'] = ' data-options="' . $core->sanitize_inputs($item['options'], array('removetags' => true)) . '"';
            }

            $attrs['data-id'] = ' data-id="' . $id . '"';
            if ($item['ignoreid'] == true) {
                unset($attrs['data-id']);
            }

            $output .= '<li' . $attrs['data-id'] . $attrs['data-button'] . $attrs['data-options'] . '><p>' . $text . '</p></li>';
            unset($attrs);
        }
        return $output;
    }

    public function parse($options = array()) {
        global $template, $headerinc, $core;

        if ($options['skip_headerinc'] != true) {
            eval("\$headerinc .= \"" . $template->get('headerinc_helptour') . "\";");
        }
        $items = $this->parse_items();
        eval("\$help_helptour = \"" . $template->get('help_helptour') . "\";");
        return $help_helptour;
    }

}
