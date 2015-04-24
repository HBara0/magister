<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Tables_class.php
 * Created:        @hussein.barakat    Apr 21, 2015 | 1:46:17 PM
 * Last Update:    @hussein.barakat    Apr 21, 2015 | 1:46:17 PM
 */

class SystemTables extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'stid';
    const TABLE_NAME = 'system_tables';
    const SIMPLEQ_ATTRS = '*';
    const DISPLAY_NAME = 'tableName';
    const UNIQUE_ATTRS = 'tableName,className';
    const CLASSNAME = __CLASS__;

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

    public function create_class($class_definition = 1, $class_functions = 1, $class_ovrwrite = 0) {
        global $core;
        //check if file already exists and notify the user
        $path = $core->settings['rootdir'].'/inc/'.$this->className.'_class.php';
        $path = 'C:\www\development\ocos\inc\\'.$this->className.'_class.php  ';
        if(file_exists($path) && $class_ovrwrite != 1) {
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

            /* check if we want the definition */
            if($class_definition == 1) {
                if($column_obj->relatedTo != 0) {
                    $ref_col_obj = new SystemTablesColumns($column_obj->relatedTo);
                    $ref_table_obj = new SystemTables($ref_col_obj->stid);
                    $reference[$column_obj->stcid]['colname'] = $column_obj->columnDbName;
                    $reference[$column_obj->stcid]['classname'] = $ref_table_obj->className;
                    $reference[$column_obj->stcid]['colsysname'] = $column_obj->columnSystemName;
                    if(!is_object($ref_col_obj) || !is_object($ref_table_obj)) {
                        $reference = null;
                    }
                }
                /* Class GET FUNCTIONS-Start */
                if(is_array($reference) && !empty($reference)) {
                    foreach($reference as $pk => $values) {
                        if(empty($values) || empty($values['classname']) || empty($values['colname'])) {
                            continue;
                        }
                        $class_geters .=
                                <<<EOD

public function get_{$values['colsysname']}(){
    return new {$values['classname']}(\$this->data['{$values['colname']}']);

   }

EOD;
                    }
                }
                else {
                    $class_geters = '';
                }
            }
            /* Class GET FUNCTIONS-END */

            if($column_obj->isPrimaryKey != 1) {
                $column_names[] = $column_obj->columnDbName;
            }
        }
        $primary_key = implode(',', $primarykey);
        $unique_attrs = implode(',', $uniques);
        /* check if we want the definition */
        if($class_definition == 1) {
            /* Parse columns for CREATE AND UPDATE-START */
            if(is_array($column_names)) {
                foreach($column_names as $column_name) {
                    switch($column_name) {
                        case('createdOn'):
                            $parse_cols_create.= "\t'$column_name' => TIME_NOW,\n";
                            break;
                        case('createdBy'):
                            $parse_cols_create.= "\t'$column_name' => \$core->user['id'],\n";
                            break;
                        case('modifiedBy'):
                            $parse_cols_update.="\t\$update_array['$column_name']= \$core->user['id'];\n";
                            break;
                        case('createdOn'):
                            $parse_cols_update.="\t\$update_array['$column_name']=\TIME_NOW;\n";
                            break;
                        default:
                            $parse_cols_create.= "\t'$column_name' => \$data['$column_name'],\n";
                            $parse_cols_update.="\t\$update_array['$column_name']=\$data['$column_name'];\n";
                            break;
                    }
                }
            }
            /* Parse columns for CREATE AND UPDATE-END */
            /* Class Def-START */
            $class_definition = <<<EOD
<?php
class $this->className extends AbstractClass {
        protected \$data = array();
        protected \$errorcode = 0;
        const PRIMARY_KEY = '$primary_key';
        const TABLE_NAME = '$this->tableName';
        const SIMPLEQ_ATTRS = '*';
        const UNIQUE_ATTRS = '$unique_attrs';
        const CLASSNAME = __CLASS__;
        const DISPLAY_NAME = '$display';


EOD;

            /* Class Def-END */
            /* Class Construct-Start */
            $class_functions = <<<EOD

public function __construct(\$id = '', \$simple = true) {
        parent::__construct(\$id, \$simple);
                }

public function create(array \$data) {
        global \$db,\$core;
        \$table_array = array(
 $parse_cols_create
                );
        \$query = \$db->insert_query(self::TABLE_NAME, \$table_array);
        if(\$query) {
            \$this->data[self::PRIMARY_KEY] = \$db->last_id();
        }
        return \$this;
    }

protected function update(array \$data) {
        global \$db;
        if(is_array(\$data)) {
$parse_cols_update
                    }
       \$db->update_query(self::TABLE_NAME, \$update_array, self::PRIMARY_KEY.'='.intval(\$this->data[self::PRIMARY_KEY]));
        return \$this;
        }
EOD;
        }
        /* Class Update-END */

        $class = $class_definition.''.$class_functions.''.$class_geters.'}';
        $result = file_put_contents($path, $class);
        if($result == false) {
            return false;
        }
        return true;
    }

}