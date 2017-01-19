<?php

/**
 * Description of HelpVideos_class
 *
 * @author zaher.reda
 */
class HelpVideos extends AbstractClass {

    protected $data = array();

    const PRIMARY_KEY = 'hvid';
    const TABLE_NAME = 'help_videos';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'hvid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        
    }

    protected function update(array $data) {
        
    }

    public function parse_link() {
        return '<a href="' . $this->link . '" target="_blank"><image src="./images/icons/question.gif" height = "16px" width = "16px"></a>';
    }

//put your code here
}
