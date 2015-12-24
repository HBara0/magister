<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Cache Class
 * $id: Cache_class.php
 * Created: 	@zaher.reda 	June 04, 2012 | 12:16 AM
 * Last Update: @zaher.reda 	June 04, 2012 | 12:16 AM
 */

class Cache {
    public $data = array();

    public function __construct() {
        $this->data = array();
    }

    public function add($reference, $content, $index = '', $appendto = '') {
        if($index == '') {
            if(!empty($appendto)) {
                $this->data{$appendto}[$reference][] = $content;
            }
            else {
                $this->data[$reference][] = $content;
            }
        }
        else {
            if(!empty($appendto)) {
                $this->data{$appendto}[$reference][$index] = $content;
            }
            else {
                $this->data[$reference][$index] = $content;
            }
        }
    }

    public function read() {
        if(is_array($this->data)) {
            foreach($this->data as $key => $val) {
                $this->{$key} = $val;
            }
        }
    }

    public function iscached($reference, $index) {
        if(isset($this->data[$reference][$index])) {
            return true;
        }
        return false;
    }

    public function incache($reference, $value) {
        if(is_array($this->data[$reference])) {
            if(in_array($value, $this->data[$reference])) {
                return true;
            }
        }
        return false;
    }

    public function get_cachedval($reference, $index = '') {
        return $this->data[$reference][$index];
    }

    public function __get($attr) {
        if(isset($this->data[$attr])) {
            return $this->data[$attr];
        }
        return false;
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function flush($reference) {
        unset($this->data[$reference]);
    }

}
?>