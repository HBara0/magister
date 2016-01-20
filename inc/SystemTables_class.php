<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Tables_class.php
 * Created:        @hussein.barakat    Apr 21, 2015 | 1:46:17 PM
 * Last Update:    @hussein.barakat    Apr 21, 2015 | 1:46:17 PM
 */
/* -------Definiton-START-------- */

class SystemTables extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'stid';
    const TABLE_NAME = 'system_tables';
    const SIMPLEQ_ATTRS = '*';
    const DISPLAY_NAME = 'tableName';
    const UNIQUE_ATTRS = 'tableName,className';
    const CLASSNAME = __CLASS__;

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db;
        if(empty($data['tableName']) || !isset($data['tableName'])) {
            $this->errorcode = 1;
            return;
        }
        $table_array = array(
                'tableName' => $data['tableName'],
                'className' => $data['className'],
        );
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            $cols_num = $db->query('SELECT COUNT(*) FROM system_tables_columns WHERE stid='.$this->data[self::PRIMARY_KEY].' GROUP BY stid');
            if($cols_num->num_rows > 0) {
                while($countcols = $db->fetch_array($cols_num)) {
                    $table_array['nbOfColumns'] = $countcols['count'];
                }
                $query = $db->insert_query(self::TABLE_NAME, $table_array);
            }
        }

        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            if(empty($data['tableName']) || !isset($data['tableName'])) {
                $this->errorcode = 1;
                return;
            }
            $table_array['tableName'] = $data['tableName'];
            $table_array['className'] = $data['className'];
            $cols_num = $db->query('SELECT COUNT(*) AS count FROM system_tables_columns WHERE stid='.$this->data[self::PRIMARY_KEY].' GROUP BY stid');
            if($cols_num->num_rows > 0) {
                while($countcols = $db->fetch_array($cols_num)) {
                    $table_array['nbOfColumns'] = $countcols['count'];
                }
            }
        }

        $db->update_query(self::TABLE_NAME, $table_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    public function create_class($view_definition = 1, $view_functions = 1, $overwrite = 0) {
        global $core;

//check if file already exists and notify the user
        $path = $core->settings['rootdir'].'/inc/'.$this->className.'_class.php';
        $path = 'C:\www\development\ocos\inc\\'.$this->className.'_class.php  ';
        if(file_exists($path) && $overwrite != 1) {
            return false;
        }
        $column_objs = SystemTablesColumns::get_data(array('stid' => $this->stid), array('returnarray' => true));
        if(!is_array($column_objs) || empty($column_objs)) {
            return false;
        }
        foreach($column_objs as $column_obj) {
            if($column_obj->isPrimaryKey == 1) {
                $primarykey[] = $column_obj->columnDbName;
            }
            if($column_obj->isUnique == 1) {
                $uniques[] = $column_obj->columnDbName;
            }
            if($column_obj->isDisplayName == 1) {
                $display = $column_obj->columnDbName;
            }
            if($column_obj->isRequired == 1) {
                $required[] = $column_obj->columnDbName;
            }
            if($column_obj->isSimple == 1) {
                $simple[] = $column_obj->columnDbName;
            }
            /* check if we want the definition */
            if($view_functions == 1 || $overwrite == 1) {
                if(isset($column_obj->relatedTo) && $column_obj->relatedTo != 0) {
                    $ref_col_obj = new SystemTablesColumns($column_obj->relatedTo);
                    $ref_table_obj = new SystemTables($ref_col_obj->stid);
                    if(!is_object($ref_col_obj) || !is_object($ref_table_obj)) {

                    }
                    else {
                        /* Class GET FUNCTIONS-Start */

                        if(empty($column_obj->columnSystemName) || empty($ref_table_obj->className) || empty($column_obj->columnDbName)) {

                        }
                        else {
                            $geters[$column_obj->columnSystemName]['classname'] = $ref_table_obj->className;
                            $geters[$column_obj->columnSystemName]['dbname'] = $column_obj->columnDbName;
                        }
                    }
                }
            }
            /* Class GET FUNCTIONS-END */

            if($column_obj->isPrimaryKey != 1) {
                $column_names[] = $column_obj->columnDbName;
            }
        }
        if(is_array($primarykey) && !empty($primarykey)) {
            $primary_key = implode(',', $primarykey);
        }
        if(is_array($uniques) && !empty($uniques)) {
            $unique_attrs = implode(',', $uniques);
        }
        if(is_array($required) && !empty($required)) {
            $required_attrs = implode(',', $required);
        }
        $simple_attrs = '*';
        if(is_array($simple) && !empty($simple)) {
            $simple_attrs = implode(',', $simple);
        }
        /* check if we want the definition */
        if($view_definition == 1 || $overwrite == 1) {
            /* Parse columns for CREATE AND UPDATE-START */
            if(is_array($column_names)) {
                foreach($column_names as $column_name) {
                    switch($column_name) {
                        case('createdOn'):
                            $create_extrafields.="\t\$table_array['$column_name']= TIME_NOW;\n";
                            break;
                        case('createdBy'):
                            $create_extrafields.="\t\$table_array['$column_name']= \$core->user['id'];\n";
                            break;
                        case('modifiedBy'):
                            $modify_extrafields.="\t\$table_array['$column_name']= \$core->user['id'];\n";
                            break;
                        case('modifiedOn'):
                            $modify_extrafields.="\t\$table_array['$column_name']= TIME_NOW;\n";
                            break;
                        default:
                            $parsedfields.= $seperator."'$column_name'";
                            $seperator = ', ';
                            break;
                    }
                }
            }
            /* Parse columns for CREATE AND UPDATE-END */
            /* Class Def-START */
            $class_definition = <<<EOD
/*-------Definiton-START--------*/
class $this->className extends AbstractClass {
        protected \$data = array();
        protected \$errorcode = 0;
        const PRIMARY_KEY = '$primary_key';
        const TABLE_NAME = '$this->tableName';
        const SIMPLEQ_ATTRS = '$simple_attrs';
        const UNIQUE_ATTRS = '$unique_attrs';
        const CLASSNAME = __CLASS__;
        const DISPLAY_NAME = '$display';
        const REQUIRED_ATTRS = '$required_attrs';

                    /*-------Definiton-END--------*/
EOD;

            /* Class Def-END */
            /* Class Construct-Start */
            $class_functions = <<<EOD
/*-------FUNCTIONS-START--------*/

public function __construct(\$id = '', \$simple = true) {
        parent::__construct(\$id, \$simple);
                }

public function create(array \$data) {
        global \$db,\$core;
        \$fields=array($parsedfields);
         if(is_array(\$fields)){
            foreach(\$fields as \$field){
                if(!is_null(\$data[\$field])){
                    \$table_array[\$field]=\$data[\$field];
                }
            }
        }
        \$this->errorcode=3;
        if(is_array(\$table_array)){
    $create_extrafields
        \$query = \$db->insert_query(self::TABLE_NAME, \$table_array);
            if(\$query) {
            \$this->errorcode=0;
            \$this->data[self::PRIMARY_KEY] = \$db->last_id();
            }
        }
        return \$this;
    }

protected function update(array \$data) {
        global \$db;
        \$fields=array($parsedfields);
         if(is_array(\$fields)){
            foreach(\$fields as \$field){
                if(!is_null(\$data[\$field])){
                    \$table_array[\$field]=\$data[\$field];
                }
            }
        }
        \$this->errorcode=3;
        if(is_array(\$table_array)){
             $modify_extrafields
              \$db->update_query(self::TABLE_NAME, \$table_array, self::PRIMARY_KEY.'='.intval(\$this->data[self::PRIMARY_KEY]));
                     \$this->errorcode=0;
              }
           return \$this;
        }

/*-------FUNCTIONS-END--------*/
EOD;
        }
        /* Class Update-END */
        if(is_array($geters) && !empty($geters)) {
            $class_geters = $this->parse_getters($geters);
        }
        if($overwrite == 0) {
            if(file_exists($path)) {
                $file_content = file_get_contents($path);
                if(!empty($file_content) && $file_content) {
                    // $class_sections = explode('/*', $file_content);
                    if($view_functions == 1) {
                        //  $class_sections[2] = $class_geters.'}';
                        $file_content = $this->replace_string('/*-------GETTER FUNCTIONS-START--------*/', '/*-------GETTER FUNCTIONS-END--------*/', $class_geters, $file_content);
                    }
                    if($view_definition == 1) {
//                        $class_sections[1] = $class_functions;
                        $file_content = $this->replace_string('/*-------FUNCTIONS-START--------*/', '/*-------FUNCTIONS-END--------*/', $class_functions, $file_content);
//                        $class_sections[0] = $class_definition;
                        $file_content = $this->replace_string('/*-------Definiton-START--------*/', '/*-------Definiton-END--------*/', $class_definition, $file_content);
                    }
                }
            }
            else {
                $file_content = '<?php'.PHP_EOL.$class_definition.PHP_EOL.$class_functions.PHP_EOL.$class_geters.PHP_EOL.'}';
            }
        }
        else {
            $file_content = '<?php'.PHP_EOL.$class_definition.PHP_EOL.$class_functions.PHP_EOL.$class_geters.PHP_EOL.'}';
        }
        $result = file_put_contents($path, $file_content);
        if($result == false) {
            return false;
        }
        return true;
    }

    public function replace_string($startpoint, $endpoint, $replacement, $original_string) {
//        $startPoint = '/* MY START STRING*/ ';
// $endPoint = '/* MY END STRING */';
// $string_original='/* MY START STRING*/ Hello Wddorld!/* MY END STRING */ ';
        $string_formatted = preg_replace('#('.preg_quote($startpoint).')(.*)('.preg_quote($endpoint).')#si', $replacement, $original_string);
        return $string_formatted;
    }

    public function parse_getters(array $getters_data) {
        if(!empty($getters_data)) {
            $class_geters .=
                    <<<EOD
/*-------GETTER FUNCTIONS-START--------*/
EOD;
            foreach($getters_data as $sysname => $get_data) {
                $class_geters .=
                        <<<EOD


public function get_{$sysname}(){
    return new {$get_data['classname']}(\$this->data['{$get_data['dbname']}']);

   }

EOD;
            }
            $class_geters .=
                    <<<EOD
/*-------GETTER FUNCTIONS-END--------*/
EOD;
            return $class_geters;
        }
        else
            return false;
    }

}