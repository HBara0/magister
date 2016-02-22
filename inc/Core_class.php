<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Core Class
 * $id: Core_class.php
 * Created:		@zaher.reda
 * Last Update: @zaher.reda 	August 24, 2012 | 10:35 AM
 */

class Core {
    public $settings = array();
    public $input = array();
    public $cookies = array();
    public $user = array();
    public $user_obj = null;
    public $usergroup = array();
    protected $magicquotes = 0;
    protected $request_method = '';
    protected $clean_variables = array(
            'int' => array(
                    'uid', 'gid', 'spid', 'cid', 'rid', 'paid', 'mrid', 'kcid', 'gpid', 'psid', 'aeid', 'sid', 'lidd'),
            'a-z' => array(
                    'sortby', 'order'
            )
    );

    public function __construct() {
        if(get_magic_quotes_gpc()) {
            $this->magicquotes = 1;
            $this->strip_slashes_array($_POST);
            $this->strip_slashes_array($_GET);
            $this->strip_slashes_array($_COOKIE);
        }

        //set_magic_quotes_runtime(0);
        @ini_set('magic_quotes_gpc', 0);
        @ini_set('magic_quotes_runtime', 0);

        $this->parse_incoming($_GET);
        $this->parse_incoming($_POST);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->request_method = 'post';
        }
        else if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->request_method = 'get';
        }

        if(@ini_get('register_globals') == 1) {
            $this->unset_globals($_POST);
            $this->unset_globals($_GET);
            $this->unset_globals($_FILES);
            $this->unset_globals($_COOKIE);
        }
        $this->clean_input();
    }

    /**
     * Filters the input based on user permissions and
     * @return type
     */
    public function filter_permissions() {
        if(!isset($this->user_obj)) {
            return;
        }
        /**
         * Default list of permission elements with their possible synonyms
         */
        $permissions_elements = array('affid' => array('affid', 'affids', 'affiliates'), 'eid' => array('eids', 'eid', 'entities'), 'spid' => array('spid', 'spids', 'suppliers'), 'cid' => array('cids', 'cid', 'customers'), 'psid' => array('psid', 'psids', 'segments', 'segment'), 'uid' => array('uid', 'uids', 'users'));

        /**
         * Retrieve the business permissions and loop over elements
         */
        //if there is an array with these names, check their permissions as well
        $additional_checks = array('filters', 'extrafilters');
        foreach($additional_checks as $arrayname) {
            if(isset($this->input[$arrayname]) && is_array($this->input[$arrayname])) {
                $extrachecks[] = $arrayname;
            }
        }
        $permissions = $this->user_obj->get_businesspermissions();
        foreach($permissions_elements as $element => $synonyms) {
            /**
             * If no permission is specified, then user can see all
             */
            if(!isset($permissions[$element])) {
                continue;
            }
            foreach($synonyms as $synonym) {
                /**
                 * If user is submiting data matching any of the permissions elements, cross check it with the permissions
                 * Otherwise, if no input, enforce permissions
                 */
                if(isset($this->input[$synonym])) {
                    /**
                     * If no permission is specified, then user can see all
                     */
                    $this->input[$synonym] = array_intersect($this->input[$synonym], $permissions[$element]);
                    $this->input[$synonym] = array_filter($this->input[$synonym]);
                }
//                else {
//                    $this->input[$element] = $permissions[$element];
//                }
                //check if there is any filter in the form and filter permissions based on them
                if(is_array($extrachecks)) {
                    foreach($extrachecks as $extrarray) {
                        if(isset($this->input[$extrarray][$synonym]) && is_array($this->input[$extrarray][$synonym])) {
                            $this->input[$extrarray][$synonym] = array_intersect($this->input[$extrarray][$synonym], $permissions[$element]);
                            $this->input[$extrarray][$synonym] = array_filter($this->input[$extrarray][$synonym]);
                        }
                    }
                }
            }
        }
    }

    protected function parse_incoming(array $array) {
        if(!is_array($array)) {
            return;
        }

        foreach($array as $key => $val) {
            $this->input[$key] = $val;
        }
    }

    protected function strip_slashes_array(&$array) {
        foreach($array as $key => $val) {
            if(is_array($array[$key])) {
                $this->strip_slashes_array($array[$key]);
            }
            else {
                $array[$key] = stripslashes($array[$key]);
            }
        }
    }

    protected function unset_globals(array $array) {
        if(!is_array($array)) {
            return;
        }

        foreach(array_keys($array) as $key) {
            unset($GLOBALS[$key]);
            unset($GLOBALS[$key]);
        }
    }

    protected function clean_input() {
        foreach($this->clean_variables as $type => $variables) {
            foreach($variables as $var) {
                if(isset($this->input[$var])) {
                    if($type == 'int') {
                        if(is_array($this->input[$var])) {
                            foreach($this->input[$var] as $key => $val) {
                                $this->input[$var][$key] = filter_var($this->input[$var][$key], FILTER_SANITIZE_NUMBER_INT); //intval($this->input[$var]);
                            }
                        }
                        else {
                            $this->input[$var] = filter_var($this->input[$var], FILTER_SANITIZE_NUMBER_INT); //intval($this->input[$var]);
                        }
                    }
                    else if($type == 'a-z') {
                        $this->input[$var] = preg_replace("#[^a-z\.\-_]#i", "", $this->input[$var]); //Remove everything except letters from A-Z . - and _
                    }
                }
            }
        }
    }

    public function sanitize_email($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    public function sanitize_URL($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    public function validtate_URL($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public function sanitize_special_chars($string) {
        return filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function sanitize_encoded($string) {
        return filter_var($string, FILTER_SANITIZE_ENCODED);
    }

    /* Clean path to prevent Directory Traversal Attacks */
    public function sanitize_path($path) {
        return str_replace(array('./', '..'), '', $path);
    }

    public function sanitize_inputs($string, array $options = array('method' => 'convert', 'removetags' => false)) {
        global $lang;
        /* Strip HTML and PHP tags from the string */
        if($options['removetags'] === true) {
            /* Decode html to ensure that content is not passed using html entity rather than character which will fail the following function */
            $string = html_entity_decode($string, ENT_QUOTES, $lang->settings['charset']);
            $string = strip_tags($string, $options['allowable_tags']);
        }

        /* 	IMPORTANT!!
         * 	NEVER remove the charset from the htmlentities and htmlspecialchars to ensure charset consistency
         */
        if($options['method'] == 'convert') {
            $string = htmlspecialchars($string, ENT_QUOTES, $lang->settings['charset']);
        }
        elseif($options['method'] == 'striponly') {
            return $string;
        }
        else {
            $string = htmlentities($string, ENT_QUOTES, $lang->settings['charset']);
        }
        return $string;
    }

    public function validate_ip($ip, $flag = '') {
        if($flag != '') {
            return filter_var($ip, FILTER_VALIDATE_IP, $flag);
        }
        else {
            return filter_var($ip, FILTER_VALIDATE_IP);
        }
    }

    public function validate_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function parse_cookies() {
        if(!is_array($_COOKIE)) {
            return;
        }

        $prefix_length = strlen(COOKIE_PREFIX);

        foreach($_COOKIE as $key => $val) {
            if($prefix_length && substr($key, 0, $prefix_length) == COOKIE_PREFIX) {
                $key = substr($key, $prefix_length);

                if($this->cookies[$key]) {
                    unset($this->cookies[$key]);
                }
            }

            if(!$this->cookies[$key]) {
                $this->cookies[$key] = $val;
            }
        }
    }

}
?>