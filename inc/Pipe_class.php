<?php

class Pipe {

    var $content = '';
    var $maildata = array();

    public function __construct() {
        $data = fopen("php://stdin", "r");
        while (!feof($data)) {
            $this->content .= fread($data, 4096);
        }
        fclose($data);

        $this->process_pipe();
    }

    private function process_pipe() {
        global $core;
        $lines = explode("\n", $this->content);

        $is_headerline = true;
        foreach ($lines as $var) {
            if ($is_headerline == true) {
                if (preg_match("/^From: (.*)/", $var, $matches)) {
                    preg_match("/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/", $matches[1], $email);
                    $this->maildata['from'] = $core->validate_email($core->sanitize_email($email[1]));
                }

                if (preg_match("/^To: (.*)/", $var, $matches)) {
                    preg_match("/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/", $matches[1], $email);
                    $this->maildata['to'] = $core->validate_email($core->sanitize_email($email[1]));
                }

                if (preg_match("/^Subject: (.*)/", $var, $matches)) {
                    $this->maildata['subject'] = $matches[1];
                }

                if (preg_match("/^Content-Transfer-Encoding: (.*)/", $var, $matches)) {
                    $this->maildata['encoding'] = trim($matches[1]);
                }
            }
            else {
                $this->maildata['message'] .= $var . '\n';
            }

            if (trim($var) == '') {
                $is_headerline = false;
            }
        }

        $this->decode_message();
    }

    private function decode_message() {
        switch (strtolower($this->maildata['encoding'])) {
            case 'base64': $this->maildata['message'] = base64_decode($this->maildata['message']);
                break;
            default: break;
        }
    }

    public function get_data() {
        return $this->maildata;
    }

}

?>