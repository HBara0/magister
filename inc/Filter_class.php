<?php

class Filter {
    public $input = array();
    protected $magicquotes = 0;
    protected $request_method = "";
    protected $clean_variables = array(
            "int" => array(
                    "rid", "gid", "uid",),
            "a-z" => array(
                    "sortby", "order"
            )
    );

    public function __construct() {

        if(get_magic_quotes_gpc()) {
            $this->magicquotes = 1;
            $this->strip_slashes_array($_POST);
            $this->strip_slashes_array($_GET);
            $this->strip_slashes_array($_COOKIE);
        }

        set_magic_quotes_runtime(0);
        @ini_set("magic_quotes_gpc", 0);
        @ini_set("magic_quotes_runtime", 0);

        $this->parse_incoming($_GET);
        $this->parse_incoming($_POST);

        if($_SERVER['REQUEST_METHOD'] == "POST") {
            $this->request_method = "post";
        }
        else if($_SERVER['REQUEST_METHOD'] == "GET") {
            $this->request_method = "get";
        }

        if(@ini_get("register_globals") == 1) {
            $this->unset_globals($_POST);
            $this->unset_globals($_GET);
            $this->unset_globals($_FILES);
            $this->unset_globals($_COOKIE);
        }
        $this->clean_input();
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
                    if($type == "int") {
                        $this->input[$var] = filter_var($this->input[$var], FILTER_SANITIZE_NUMBER_INT); //intval($this->input[$var]);
                    }
                    else if($type == "a-z") {
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

    public function sanitize_special_chars($string) {
        return filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public function sanitize_encoded($string) {
        return filter_var($string, FILTER_SANITIZE_ENCODED);
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

}
?>