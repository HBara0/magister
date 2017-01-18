<?php

class Download {

    private $file_url = '';
    private $file = array();
    private $config = array();

    /*
      Class conctruct function
      @param	$tablename				String		Name of the table where the file into are
      @param	$fileattr				String		Name of the table attribute that holds the file name
      @param 	$mainid					Array		Array containing the main id for the query WHERE clode. Array should have index and value correspoding to attr and value
      @param	$file_real_path			String		The actual path for the file
      @param	$options				Array		Additional optional options for the query
     */

    public function __construct($tablename = '', $fileattr = '', array $mainid = array(), $file_real_path = '', array $options = array()) {
        if (!empty($mainid)) {
            $this->config = array('tablename' => $tablename, 'fileattr' => $fileattr, 'options' => $options);

            if (!isset($this->config['options']['titleattr'])) {
                $this->config['options']['titleattr'] = $fileattr;
            }
            $this->set_from_db($tablename, $fileattr, $mainid, $file_real_path, $options);
        }
    }

    private function set_from_db($tablename, $fileattr, array $mainid, $file_real_path, array $options = array()) {
        global $db;

        if (!empty($mainid)) {
            $key = key($mainid);
            $val = $mainid[$key];
        }
        else {
            error('Invalid file data');
            exit;
        }

        if (isset($this->config['options']['titleattr']) && $this->config['options']['titleattr'] != $fileattr) {
            $query_select = ', ' . $this->config['options']['titleattr'];
        }
        $query = $db->query("SELECT " . $db->escape_string($fileattr) . $query_select . " FROM " . Tprefix . "" . $db->escape_string($tablename) . " WHERE " . $db->escape_string($key) . "=" . $db->escape_string($val) . " LIMIT 0, 1");
        if ($db->num_rows($query) > 0) {
            $this->file = $db->fetch_assoc($query);

            $slash = '/';
            if (preg_match("/\/\z/i", $file_real_path)) {
                $slash = '';
            }
            $this->file_url = $file_real_path . $slash . $options['nameprefix'] . $this->file[$fileattr];
        }
        else {
            error('File does not exist');
            exit;
        }
    }

    /*
      If file to be downloaded is not in the DB, link can be set through this function
      @param	$path	String		Path to the file
     */

    public function set_real_path($path) {
        if (empty($path)) {
            return false;
        }
        $this->file_url = realpath($path);
    }

    /*
      Download file by using location (TO BE DEPRECATED)
     */

    public function download_file() {
        header("Content-type: application/x-msdownload");
        header("Pragma: no-cache");
        header("Expires: 0");
        header('location:' . $this->file_url);
    }

    /*
      Stream file to a download
      @param	$also_delete	Boolean		Whether to delete original file after download or not (useful for temp files)
     */

    public function stream_file($also_delete = false) {
        if (!file_exists($this->file_url)) {
            return;
        }
        if (empty($this->config['options']['titleattr'])) {
            $this->config['options']['titleattr'] = $this->config['options']['fileattr'];
        }

        if (empty($this->file[$this->config['options']['titleattr']])) {
            $this->file[$this->config['options']['titleattr']] = basename($this->file_url);
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Expires: 0');
        header("Content-Length: " . (string) (filesize($this->file_url)));
        header('Content-Disposition: attachment; filename="' . $this->file[$this->config['options']['titleattr']] . '"');
        header("Content-Transfer-Encoding: binary\n");

        readfile($this->file_url);

        if ($also_delete === true) {
            unlink($this->file_url);
        }
    }

}

?>