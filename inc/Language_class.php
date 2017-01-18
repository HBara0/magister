<?php

class Language {

    protected $language = 'english', $path, $area_path = '';

    public function __construct($language, $area = 'user', $path = '') {
        $this->set_path($path);
        $this->set_area($area);
        $this->set_language($language);
    }

    protected function set_language($language) {
        if ($language == '') {
            $this->language = 'english';
        }
        else {
            $this->language = preg_replace("#[^a-z0-9\-_]#i", '', $language);
        }

        if (!$this->language_exists($this->language)) {
            die("{$language} is not installed");
        }

        require $this->path . '/' . $this->language . '.php';
        $this->settings = $langinfo;
    }

    protected function set_path($path) {
        if ($path == '') {
            $path = ROOT . INC_ROOT . 'languages';
        }
        $this->path = $path;
    }

    protected function set_area($area) {
        if ($area == 'admin') {
            $this->area_path = 'admin/';
        }
    }

    protected function language_exists($language) {
        $language = preg_replace("#[^a-z0-9\-_]#i", '', $language);
        if (file_exists($this->path . '/' . $language . '.php')) {
            return true;
        }
        else {
            return false;
        }
    }

    public function language_file_exists($file_name) {
        $language_file = $this->path . '/' . $this->language . '/' . $this->area_path . $file_name . '.lang.php';
        if (file_exists($language_file)) {
            return true;
        }
        else {
            return false;
        }
    }

    public function load($file_name) {
        if (!$this->language_file_exists($file_name)) {
            $this->language = 'english';
            if (!$this->language_file_exists($file_name)) {
                die("{$file_name} was not found");
            }
        }

        $language_file = $this->path . '/' . $this->language . '/' . $this->area_path . $file_name . '.lang.php';

        require_once $language_file;

        if (is_array($lang)) {
            foreach ($lang as $key => $val) {
                if (!$this->$key || $this->$key != $val) {
                    //$val = preg_replace("#\{([0-9]+)\}#", "%$1\$s", $val);
                    $this->$key = $val;
                }
            }
        }
    }

    public function sprint($string) {
        $arguments = func_get_args();
        $args_num = count($arguments);

        for ($i = 1; $i < $args_num; $i++) {
            $string = str_replace('{' . $i . '}', $arguments[$i], $string);
        }
        return $string;
    }

    public function get_languages() {
        $dir = @opendir($this->path);

        while (false !== ($lang = readdir($dir))) {
            $langfile_info = pathinfo($path . '/' . $lang);
            if ($lang != '.' && $lang != '..' && $langfile_info['extension'] == 'php') {
                $lname = str_replace('.' . $langfile_info['extension'], '', $lang);

                require $this->path . '/' . $lang;
                $languages[$lname] = $langinfo['name'];
            }
        }
        @ksort($languages);
        return $languages;
    }

    public function get_languages_db() {
        global $db;

        $query = $db->query('SELECT * FROM ' . Tprefix . 'system_languages ORDER BY name ASC');
        while ($language = $db->fetch_assoc($query)) {
            $languages[$language['slid']] = $language;
        }

        return $languages;
    }

    public function get_list_languages() {
        global $db, $core;

        $listquery = $db->query('SELECT DISTINCT(slv.fileName), slv.slvid, slvv.timeCreated as timeCreated, slvv.timeModified as timeModified
								FROM ' . Tprefix . 'system_langvariables slv
								JOIN ' . Tprefix . 'system_languages_varvalues slvv ON (slv.slvid=slvv.variable)
								GROUP BY fileName
								ORDER BY fileName, timeCreated DESC');
        if ($db->num_rows($listquery) > 0) {
            while ($listlanguage = $db->fetch_assoc($listquery)) {
                $languagefiles[$listlanguage['slvid']] = $listlanguage;
            }
            return $languagefiles;
        }
        return false;
    }

    public function rebuild_langfile($file, array $languages) {
        global $db;

        $mode = 'w';
        foreach ($languages as $langid => $langname) {
            $variables = '';
            if (!file_exists(ROOT . 'inc/languages/' . $langname . '/' . $file . '.lang.php')) {
                $mode = 'x';
            }

            $query = $db->query("SELECT name, value
			FROM " . Tprefix . "system_langvariables slv
			JOIN " . Tprefix . "system_languages_varvalues slvv ON (slv.slvid=slvv.variable)
			WHERE fileName='" . $db->escape_string($file) . "' AND lang='" . $db->escape_string($langid) . "'
			ORDER BY name ASC");
            if ($db->num_rows($query) > 0) {
                while ($variable = $db->fetch_array($query)) {
                    $variables .= "\$lang['" . $variable['name'] . "'] = '" . addslashes($variable['value']) . "';\n";
                }
                $settings = "<" . "?php\n/*********************************\ \n  DO NOT EDIT THIS FILE\n PLEASE USE the Admin CP\n\*********************************/\n\n{$variables}?" . ">";
                $langfile = @fopen(ROOT . 'inc/languages/' . $langname . '/' . $file . '.lang.php', $mode);
                $write = @fwrite($langfile, $settings);
                if ($write === false) {
                    return false;
                }
                @fclose($langfile);
            }
        }
        return true;
    }

}

?>