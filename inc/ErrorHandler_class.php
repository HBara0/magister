<?php

define("SQL_ERROR", 20);
define("CUSTOM_INLINE_ERROR", 30);

class ErrorHandler {

    public $recorded_errors = array();
    private $valid_options = array('content', 'redirect_url', 'noexit', 'display');
    private $error_types = array(
        E_ERROR => 'Error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parsing Error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core Error',
        E_CORE_WARNING => 'Core Warning',
        E_COMPILE_ERROR => 'Compile Error',
        E_COMPILE_WARNING => 'Compile Warning',
        E_DEPRECATED => 'Deprecated Warning',
        E_USER_ERROR => 'User Error',
        E_USER_WARNING => 'User Warning',
        E_USER_NOTICE => 'User Notice',
        E_STRICT => 'Runtime Notice',
        E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        CUSTOM_INLINE_ERROR => 'Application Custom Error',
        SQL_ERROR => 'SQL Error'
    );
    private $ignored_types = array(
        E_DEPRECATED,
        E_NOTICE,
        E_USER_NOTICE,
        E_STRICT
    );

    public function __construct($secondary = false) {
        if ($secondary == true) {
            $this->recorded_errors = array();
        }
        else {
            set_error_handler(array(&$this, 'error')); //, array_diff($this->error_types, $this->ignored_types)
        }
    }

    public function error($type, $message, $file = null, $line = 0) {
        if (error_reporting() == 0) {
            return;
        }

        if (in_array($type, $this->ignored_types)) {
            return;
        }

        /* Record error to log file */
        if ($type != 'CUSTOM_INLINE_ERROR') {
            if (is_array($message)) {
                error_log(implode(' ', $message) . "\n", 0);
            }
            else {
                error_log($message . "\n", 0);
            }

            if (class_exists('DevelopmentBugs')) {
                $bug = new DevelopmentBugs();
                $bug_details = array('description' => $message, 'stackTrace' => serialize(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)), 'file' => $file, 'line' => $line);
                $bug->set($bug_details)->save()->send();
            }
        }

        if (is_array($message)) {
            if ($message['display'] == 'inline') {
                $this->output_errors_inline($message['content']);
            }
            else {
                $this->output_errors_box($message['content'], $message['redirect_url'], $message['noexit']);
            }
        }
        else {
            if (!empty($line)) {
                $message .= '<br />Line: ' . $line;
            }

            if (!empty($file)) {
                $message .= '<br />File: ' . $file;
            }
            $this->output_errors_box($message);
        }
    }

    public function trigger($message = '', $options = '', $type = E_USER_ERROR) {
        global $lang;

        if (!$message) {
            $message = $lang->na;
        }

        //if($type == SQL_ERROR || $type == CUSTOM_INLINE_ERROR) {
        $message = array('content' => $message);
        if ($type == CUSTOM_INLINE_ERROR) {
            $message['display'] = 'inline';
        }

        if (is_array($options) && !empty($options)) {
            foreach ($options as $options_key => $option_value) {
                if (in_array($option, $this->valid_options)) {
                    $message[$option_key] = $option_value;
                }
            }
        }
        $this->error($type, $message);
        /* }
          else
          {
          trigger_error($message, $type);
          } */
    }

    public function record($reference, $content, $appendto = '') {
        if (!empty($appendto)) {
            $this->recorded_errors{$appendto}[$reference][] = $content;
        }
        else {
            if (is_array($this->recorded_errors[$reference]) && !in_array($content, $this->recorded_errors[$reference])) {
                $this->recorded_errors[$reference][] = $content;
            }
            elseif (!is_array($this->recorded_errors[$reference])) {
                $this->recorded_errors[$reference][] = $content;
            }
        }
    }

    public function output_errors_box($message, $redirect_url = '', $noexit = false) {
        global $template, $lang, $settings, $headerinc;

        if (is_array($message)) {
            $error_message = implode('<br />', $message);
        }
        else {
            $error_message = $message;
        }

        if (!empty($redirect_url)) {
            $redirect = '<meta http-equiv="refresh" content="3;URL=' . $redirect_url . '" />';
        }

        $core->settings['systemtitle'] = $settings['systemtitle'];
        eval("\$errorpage = \"" . $template->get('errorpage') . "\";");

        output_page($errorpage);

        if ($noexit == false) {
            exit;
        }
    }

    public function get_errors_inline($message = '') {
        $output = '';
        if (!empty($message)) {
            $output = '&bull; ' . $message;
        }
        else {
            if (is_array($this->recorded_errors) && !empty($this->recorded_errors)) {
                $output = $this->parse_errors_inline($this->recorded_errors);
            }
        }
        return $output;
    }

    private function parse_errors_inline(array $data, $is_recursive = false) {
        global $lang;

        foreach ($data as $type => $content) {
            if (empty($lang->$type)) {
                $type_ourput = $type;
            }
            else {
                $type_ourput = $lang->$type;
            }
            if ($is_recursive == false) {
                $output .= '<strong>' . $type_ourput . '</strong><ol>';
            }

            if (is_array($content)) {
                if ($is_recursive == true) {
                    $output .= '<strong>' . $type_ourput . '</strong><ol>';
                }
                $output .= $this->parse_errors_inline($content, true);

                if ($is_recursive == true) {
                    $output .= '</ol>';
                }
            }
            else {
                $output .= '<li>' . $content . '</li>';
            }
            if ($is_recursive == false) {
                $output .= '</ol>';
            }
        }
        return $output;
    }

    public function output_errors_inline($message = '') {
        echo $this->get_errors_inline($message);
    }

    public function parse_errorcode($errorcode) {
        global $lang;

        switch ($errorcode) {
            case 0:
            case 1:
                return true;
            case 2:
                return $lang->fillallrequiredfields;
            case 301:
                return $lang->sectionnopermission;
            case 601:
                return $lang->errorsaving;
            case 602:
                return $lang->entryexists;
            default:
                return $lang->error;
        }
    }

}

?>