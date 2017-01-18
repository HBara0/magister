<?php

class Templates {

    protected $cache = array();

    public function get($title, $raw = 1) {
        global $db, $core, $errorhandler;

        if (!isset($this->cache[$title])) {
            if (TEMPLATES_SYSTEM == 'FILE') {
                $base_dir = ROOT . INC_ROOT . 'templates/';
                $tplfilepath = $base_dir . $core->sanitize_path($title) . '.tpl';
                if (file_exists($tplfilepath)) {
                    $handle = fopen($tplfilepath, 'r');
                    $template_content = fread($handle, filesize($tplfilepath));
                    fclose($handle);
                }
                else {
                    $errorhandler->trigger('Template: ' . $title . ' could not be found.');
                }
            }
            else {
                $template_content = $db->fetch_field($db->query("SELECT template FROM " . Tprefix . "templates WHERE title='" . $db->escape_string($title) . "'"), 'template');
            }
            $this->cache[$title] = trim($template_content);
            if (empty($this->cache[$title])) {
                $this->cache[$title] = 'Template: ' . $title . ' could not be found.<br />';
            }
        }
        $template = "<!-- start: {$title} -->\n" . $this->cache[$title] . "\n<!-- end: {$title} -->";

        if ($raw == 1) {
            $template = str_replace("\\'", "'", $db->escape_string($template));
        }

        return $template;
    }

    function dump_templates_to_file_folder() {
        global $db, $template;
        $base_dir = ROOT . INC_ROOT . 'templates/';
        $content = '<div style = "padding:20px;"><hr>';
        $templates_query = $db->query('SELECT * FROM ' . Tprefix . 'templates');

        if ($db->num_rows($templates_query) > 0) {
            while ($singletemplate = $db->fetch_assoc($templates_query)) {
                $content.=' < br>' . $base_dir . $singletemplate['title'];
                try {
                    $filename = $base_dir . $singletemplate['title'] . '.tpl';
                    $filehandle = fopen($filename, 'w');
                    fwrite($filehandle, $singletemplate['template']);
                    fclose($filehandle);
                    $content.=' V';
                }
                catch (Exception $e) {
                    $content.=' X ' . $e->getMessage();
                }
            }

            echo $content;
        }
        else {
            echo 'Nothing to copy.';
        }
    }

}

?>