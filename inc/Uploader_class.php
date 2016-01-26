<?php

class Uploader {
    protected $target_path = './uploads';
    protected $fieldname;
    protected $file = array();
    protected $cleaned_name = array();
    protected $file_path = array();
    protected $file_data = '';
    /* 	STATUS LEGEND
      0	unknown error
      1	max size exceeded
      2	type not allowed
      3	file exists
      4	successfully moved
     */
    protected $upload_status = array();
    protected $rename = 0;

    /* FTP Settings */
    protected $ftpconnection = '';
    protected $ftp_settings = array();
    protected $ftp_destfolder = '';

    public function __construct($fieldname = '', array $file = array(), array $allowed_types = array(), $upload_type = 'putfile', $max_size = '-1', $array_of_files = 0, $rename = 0) {
        if($upload_type != 'constructonly') {
            $this->set_options($fieldname, $file, $allowed_types, $upload_type, $max_size, $array_of_files, $rename);
        }
        //$this->process_file();
    }

    public function set_options($fieldname, array $file, array $allowed_types, $upload_type = 'putfile', $max_size = '-1', $array_of_files = 0, $rename = 0) {
        $this->fieldname = addslashes($fieldname);
        $this->raw_file = $file;
        $this->rename = $rename;
        $this->array_of_files = $array_of_files;
        $this->max_size = $max_size;
        $this->upload_type = $upload_type;
        $this->allowed_types = $allowed_types;
    }

    public function process_file() {
        if($this->array_of_files == 0) {
            foreach($this->raw_file[$this->fieldname] as $key => $val) {
                $this->file[$this->fieldname][$key][0] = $val;
            }

            if($this->file[$this->fieldname]['error'][0] != UPLOAD_ERR_OK) {
                echo $this->file[$this->fieldname]['error'][0];
            }
            else {
                if($this->check_max_size($this->max_size, 0)) {
                    if($this->validate_type($this->allowed_types, 0)) {
                        if($this->upload_type == 'readonly') {
                            $file_upload_status = $this->read_file(0);
                        }
                        elseif($this->upload_type == 'ftp') {
                            $file_upload_status = $this->ftp_file(0);
                        }
                        else {
                            $file_upload_status = $this->put_file(0);
                        }
                        if($file_upload_status === true) {
                            $this->set_status(4);
                        }
                    }
                    else {
                        $this->set_status(2);
                    }
                }
                else {
                    $this->set_status(1);
                }
            }
        }
        else {
            $this->file = $this->raw_file;
            foreach($this->file[$this->fieldname]['error'] as $key => $error) {
                if($error == UPLOAD_ERR_OK) {
                    if($this->check_max_size($this->max_size, $key)) {
                        if($this->validate_type($this->allowed_types, $key)) {
                            if($this->upload_type == 'readonly') {
                                $file_upload_status = $this->read_file($key);
                            }
                            elseif($this->upload_type == 'ftp') {
                                $file_upload_status = $this->ftp_file($key);
                            }
                            else {
                                $file_upload_status = $this->put_file($key);
                            }
                            if($file_upload_status === true) {
                                $this->set_status(4, $key);
                            }
                        }
                        else {
                            $this->set_status(2, $key);
                        }
                    }
                    else {
                        $this->set_status(1, $key);
                    }
                }
                else {
                    echo $this->file[$this->fieldname]['error'][$key];
                }
            }
        }
    }

    private function format_path($key) {
        if($this->rename == 0) {
            $this->cleaned_name[$key] = $this->clean_file_name(basename($this->file[$this->fieldname]['name'][$key]));
        }
        else {
            $path_info = pathinfo($this->file[$this->fieldname]['name'][$key]);
            $this->cleaned_name[$key] = rand(0, 99).'_'.time().'.'.$path_info['extension'];
            if(!empty($this->file[$this->fieldname]['newname'][$key])) {
                $this->cleaned_name[$key] = $this->file[$this->fieldname]['newname'][0].'.'.$path_info['extension'];
            }
        }

        $this->file_path[$key] = $this->target_path.'/'.$this->cleaned_name[$key];

        if($this->rename == 0) {
            if(file_exists($this->file_path[$key])) {
                return false;
            }
        }
        return true;
    }

    private function put_file($key) {
        if(!is_uploaded_file($this->file[$this->fieldname]['tmp_name'][$key])) {
            $this->set_status(0, $key);
            return false;
        }

        if($this->format_path($key)) {
            $upload = move_uploaded_file($this->file[$this->fieldname]['tmp_name'][$key], $this->file_path[$key]);
            if($upload) {
                $this->file[$this->fieldname]['destination'][$key] = $this->file_path[$key];
                return true;
            }
            else {
                $this->set_status(0, $key);
                return false;
            }
        }
        else {
            $this->set_status(3, $key);
            return false;
        }
    }

    private function read_file($key) {
        if(!is_uploaded_file($this->file[$this->fieldname]['tmp_name'][$key])) {
            $this->set_status(0, $key);
            return false;
        }
        $this->file_data = file_get_contents($this->file[$this->fieldname]['tmp_name'][$key]);
        if(strlen($this->file_data) > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    private function ftp_file($key) {
        if(!isset($this->ftpconnection)) {
            $this->ftpconnection = $this->establish_ftp();
        }

        if($this->format_path($key)) {
            ftp_chdir($this->ftpconnection, $this->target_path);
            $upload = ftp_put($this->ftpconnection, $this->cleaned_name[$key], $this->file[$this->fieldname]['tmp_name'][$key], FTP_BINARY);
            if($upload == false) {
                $this->set_status(0, $key);
                $this->close_ftp();
                return false;
            }
            $chmod = ftp_chmod($this->ftpconnection, 0644, $this->file_path[$key]);
        }

        return true;
    }

    /* 	public function ftp_custom_file($local, $remote) {
      if(!isset($this->ftpconnection)) {
      $this->ftpconnection = $this->establish_ftp();
      }

      if($this->format_path($key)) {
      $upload = ftp_put($this->ftpconnection, $remote, $local, FTP_BINARY);
      if($upload == false) {
      $this->set_status(0, $key);
      $this->close_ftp();
      return false;
      }
      $chmod = ftp_chmod($this->ftpconnection, 0644, $this->file_path[$key]);
      }

      return true;
      } */
    public function establish_ftp(array $settings = array()) {
        if(!empty($settings)) {
            $this->ftp_settings = $settings;
        }

        if(!isset($this->ftp_settings['server'], $this->ftp_settings['username'], $this->ftp_settings['password'])) {
            return false;
        }

        if(is_empty($this->ftp_settings['server'], $this->ftp_settings['username'], $this->ftp_settings['password'])) {
            return false;
        }

        $connection = ftp_connect($this->ftp_settings['server']);
        $login = ftp_login($connection, $this->ftp_settings['username'], $this->ftp_settings['password']);

        if(!$connection || !$login) {
            return false;
        }

        if($this->ftp_settings['passive'] == 1) {
            ftp_pasv($connection, true);
        }

        $this->ftpconnection = $connection;
        return $connection;
    }

    public function close_ftp($connection = '') {
        if(empty($connection)) {
            $connection = $this->ftpconnection;
        }
        if(is_object($connection)) {
            if(ftp_close($connection) === TRUE) {
                return true;
            }
        }
        return false;
    }

    protected function clean_file_name($file) {
        $to_remove = array(' ', '`', '"', '\'', '\\', '/');
        return str_replace($to_remove, '', $file);
    }

    protected function validate_type(array $types, $file_key) {
        $found = false;
        foreach($types as $key => $val) {
            if(strtolower($val) == $this->file[$this->fieldname]['type'][$file_key]) {
                $found = true;
                break;
            }
        }
        return $found;
    }

    protected function check_max_size($max_size, $key) {
        if($this->file[$this->fieldname]['size'][$key] > 0) {
            if($max_size == '-1') {
                return true;
            }
            else {
                if($this->file[$this->fieldname]['size'][$key] <= $max_size) {
                    return true;
                }
                else {
                    return false;
                }
            }
        }
        else {
            return false;
        }
    }

    public function resize($new_width = 200, $newfile = '') {
        if(!is_array($this->file[$this->fieldname]['type'])) {
            return;
        }
        foreach($this->file[$this->fieldname]['type'] as $key => $val) {
            if(!$this->validate_type($this->allowed_types, $key)) {
                continue;
            }

            switch($val) {
                case 'image/gif': $imagecreatefunc = 'imagecreatefromgif';
                    $imagefunc = 'imagegif';
                    $quality = 85;
                    break;
                case 'image/jpeg': $imagecreatefunc = 'imagecreatefromjpeg';
                    $imagefunc = 'imagejpeg';
                    $quality = 85;
                    break;
                case 'image/png': $imagecreatefunc = 'imagecreatefrompng';
                    $imagefunc = 'imagepng';
                    $quality = 8;
                    break;
                case 'image/bitmap': $imagecreatefunc = 'imagecreatefromwbmp';
                    $imagefunc = 'imagewbmp';
                    $quality = '';
                    break;
                default: unset($this->file_path[$key]);
                    $this->set_status(0, $key);
                    break;
            }
            $source = $imagecreatefunc($this->file[$this->fieldname]['destination'][$key]);
            list($width, $height) = getimagesize($this->file[$this->fieldname]['destination'][$key]);

            if($width < $new_width) {
                continue;
            }

            $new_height = ($height / $width) * $new_width;
            $temporary = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($temporary, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            if(empty($newfile)) {
                $newfile = $this->file[$this->fieldname]['destination'][$key];
            }
            if(file_exists($newfile)) {
                unlink($newfile);
            }
            $imagefunc($temporary, $newfile, $quality);
        }
    }

    public function set_upload_path($path = './uploads') {
        $this->target_path = $path;
    }

    private function set_status($new_status, $key = 0) {
        $this->upload_status[$key] = $new_status;
    }

    public function get_status($key = 0) {
        return $this->upload_status[$key];
    }

    public function get_status_array() {
        return $this->upload_status;
    }

    public function parse_status($error_code = 0) {
        switch($error_code) {
            case 0: $upload_result = "<span class='red_text'>Error while uploading</span>";
                break;
            case 1: $upload_result = "<span class='red_text'>The file is too big</span>";
                break;
            case 2: $upload_result = "<span class='red_text'>The file type is not allowed</span>";
                break;
            case 3: $upload_result = "<span class='red_text'>A file with the same name already exists</span>";
                break;
            case 4: $upload_result = "<span class='green_text'>Upload successful</span>";
                break;
        }
        return $upload_result;
    }

    public function get_filename($key = 0) {
        return $this->cleaned_name[$key];
    }

    public function get_fileinfo($key = 0) {
        $file_path_info = pathinfo($this->file[$this->fieldname]['name'][$key]);

        $file_info = array(
                'size' => $this->file[$this->fieldname]['size'][$key],
                'type' => $this->file[$this->fieldname]['type'][$key],
                'extension' => $file_path_info['extension'],
                'name' => $this->cleaned_name[$key],
                'originalname' => $this->file[$this->fieldname]['name'][$key]
        );
        return $file_info;
    }

    public function get_filesinfo() {
        foreach($this->file[$this->fieldname]['name'] as $key => $val) {
            $info[$key] = $this->get_fileinfo($key);
        }
        return $info;
    }

    public function get_filedata($key = 0) {
        return $this->file_data;
    }

}
?>