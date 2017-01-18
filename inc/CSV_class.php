<?php

class CSV {

    private $data, $csvheader = array();
    private $raw_data = '';
    private $filename = '';
    private $rowscount = 0;

    /* 	STATUS LEGEND
      0	unknown error
      1	file doesn't exist
      3	empty string
      4	successfully processed
     */
    private $status = 0;
    private $settings = array('type' => 1,
        'frowheader' => true,
        'delimiter' => ',',
        'enclosure' => '"'
    );

    public function __construct($data, $type = 1, $frowheader = true, $delimiter = ',') {
        $this->settings['type'] = $type;
        $this->settings['frowheader'] = $frowheader;
        $this->settings['delimiter'] = $delimiter;

        if ($this->settings['type'] == 1) {
            $this->filename = $data;
            if ($this->checkfile() === false) {
                $this->status = 1;
                return false;
            }
        }
        else {
            $this->raw_data = $data;
        }
    }

    private function my_str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = '\\') {
        $bs = '\\';
        $enc = $bs . $enclosure;
        $esc = $bs . $escape;
        $delim = $bs . $delimiter;
        $encesc = ($enc == $esc) ? $enc : $enc . $esc;
        $pattern = "/($enc(?:[^$encesc]|$esc$enc)*$enc|[^$enc$delim]*)$delim/";
        preg_match_all($pattern, $input . $delimiter, $matches);
        $parts = array();
        foreach ($matches[1] as $part) {
            $len = strlen($part);
            if ($len >= 2 && $part{0} == $enclosure) {
                $part = substr($part, 1, $len - 2);
                $part = str_replace($escape . $enclosure, $enclosure, $part);
            }
            $parts[] = $part;
        }
        return $parts;
    }

    public function readdata_string(array $filter = array(), $terminator = "\n") {
        if (empty($this->raw_data)) {
            $this->status = 3;
            return false;
        }

        $data_source = explode($terminator, $this->raw_data);

        foreach ($data_source as $value) {
            if (empty($value)) {
                continue;
            }

            if (function_exists('str_getcsv')) {
                $data_source_seperated = str_getcsv($value, $this->settings['delimiter'], $this->settings['enclosure'], $this->settings['escape']);
            }
            else {
                $data_source_seperated = $this->my_str_getcsv($value, $this->settings['delimiter'], $this->settings['enclosure'], $this->settings['escape']);
            }

            foreach ($data_source_seperated as $key => $val) {
                if ($this->rowscount == 0 && $this->settings['frowheader'] == true) {
                    if (empty($filter)) {
                        $this->csvheader[$key] = trim($val);
                    }
                    else {
                        //foreach($val as $k => $v) {
                        if (in_array($val, $filter)) {
                            $this->csvheader[$key] = trim($val);
                        }
                        //}
                    }
                }
                else {
                    if ($this->settings['frowheader'] == true) {
                        $data_array_index = $this->csvheader[$key];
                    }
                    else {
                        $data_array_index = $key;
                    }
                    //foreach($val as $k => $v) {
                    if (in_array($data_array_index, $filter)) {
                        $this->data[$this->rowscount][$data_array_index] = trim($val);
                    }
                    else {
                        if (empty($filter)) {
                            $this->data[$this->rowscount][$data_array_index] = trim($val);
                        }
                    }
                    //}
                }
            }
            $this->rowscount++;
        }
    }

    public function write_file($data = array()) {
        if (empty($data)) {
            return;
        }
        $data_source = @fopen($this->filename, 'w+');
        /* prepare header data */
        $data_header = (array_keys($data[key($data)]->get()));
        /* write the headers to csv */
        fputcsv($data_source, $data_header, ',', ',');
        foreach ($data as $datatowrite) {
            fputcsv($data_source, $datatowrite->get(), ',', ',');
        }
        /** rewrind the "file" with the csv lines * */
        fseek($data_source, 0);
        /** Send file to browser for download */
        //fpassthru($data_source);
    }

    public function readdata_file(array $filter = array()) {
        $data_source = @fopen($this->filename, 'r');

        while ($content = fgetcsv($data_source, filesize($this->filename), $this->settings['delimiter'])) {
            if ($this->rowscount == 0 && $this->settings['frowheader'] == true) {
                foreach ($content as $key => $val) {
                    if (in_array($val, $filter)) {
                        $this->csvheader[$key] = trim($val);
                    }
                    else {
                        if (empty($filter)) {
                            $this->csvheader[$key] = trim($val);
                        }
                    }
                }
            }
            else {
                foreach ($content as $key => $val) {
                    if ($this->settings['frowheader'] == true) {
                        $data_array_index = $this->csvheader[$key];
                    }
                    else {
                        $data_array_index = $key;
                    }

                    if (in_array($data_array_index, $filter)) {
                        $this->data[$this->rowscount][$data_array_index] = trim($val);
                    }
                    else {
                        if (empty($filter)) {
                            $this->data[$this->rowscount][$data_array_index] = trim($val);
                        }
                    }
                }
            }
            $this->rowscount++;
        }
        return $this->data;
    }

    public function importdata($table, array $filter = array()) {
        global $db;

        if (empty($this->data)) {
            if ($this->settings['type'] == 1) {
                $this->readdata_file($filter);
            }
            else {
                $this->readdata_string($filter);
            }
        }

        foreach ($this->data as $key => $val) {
            $db->insert_query($table, $val);
        }
    }

    public function get_data($transpose = false) {
        if ($transpose == true) {
            return $this->transpose_data($this->data);
        }
        else {
            return $this->data;
        }
    }

    public function get_header() {
        return $this->csvheader;
    }

    public function get_status() {
        return $this->status;
    }

    private function checkfile() {
        if (file_exists($this->filename)) {
            return true;
        }
        else {
            return false;
        }
    }

    private function transpose_data(array $data) {
        foreach ($data as $key => $val) {
            foreach ($val as $k => $v) {
                $new_array[$k][$key] = $v;
            }
        }
        return $new_array;
    }

}

?>