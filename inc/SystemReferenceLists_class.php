<?php
/* -------Definiton-START-------- */

class SystemReferenceLists extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'srlid';
    const TABLE_NAME = 'system_referencelists';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'srlid,referenceType';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'name';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'name' => $data['name'],
                'referenceType' => $data['referenceType'],
                'selectorType' => $data['selectorType'],
        );
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            $update_array['name'] = $data['name'];
            $update_array['referenceType'] = $data['referenceType'];
            $update_array['selectorType'] = $data['selectorType'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    public function get_referenecelines() {
        return SystemReferenceListsLines::get_data(array('srlid' => $this->data[self::PRIMARY_KEY]), array('returnarray' => true));
    }

    public function parse_listlines() {
        $lines = SystemReferenceListsLines::get_data(array('srlid' => $this->data[self::PRIMARY_KEY], 'type' => 'list'), array('returnarray' => true));
        if(is_array($lines)) {
            $l_rowid = 1;
            foreach($lines as $line) {
                $check_active = '';
                if($line->isActive == 1) {
                    $check_active = 'checked="checked"';
                }
                $line_output.='<tr id='.$l_rowid.'><div>';
                $line_output.= '<td><input type="text" required="required" name="line['.$line->inputChecksum.'][name]" value="'.$line->name.'"></td>';
                $line_output.= '<input type="hidden" value="'.$this->srlid.'" name="line['.$line->inputChecksum.'][srlid]">';
                $line_output.= '<input type="hidden" value="'.$this->referenceType.'" name="line['.$line->inputChecksum.'][type]">';
                $line_output.= '<td><input type="text" required="required" name="line['.$line->inputChecksum.'][title]" value="'.$line->title.'"></td>';
                $line_output.= '<td><input type="text" required="required" name="line['.$line->inputChecksum.'][value]" value="'.$line->value.'"></td>';
                $line_output.= '<td><input type="number" name="line['.$line->inputChecksum.'][sequence]" value="'.$line->sequence.'"></td>';
                $line_output.= '<td><textarea name="line['.$line->inputChecksum.'][description]">'.$line->description.'</textarea></td>';
                $line_output.= '<td><input type="checkbox" value="1" name="line['.$line->inputChecksum.'][isActive]" '.$check_active.'></td>';
                $line_output.='</div></tr>';
                $l_rowid++;
            }
        }
        $line_output = array($line_output, $l_rowid);
        return $line_output;
    }

    public function parse_tablelines() {
        $tables = SystemReferenceListsLines::get_data(array('srlid' => $this->data[self::PRIMARY_KEY], 'type' => 'table'), array('returnarray' => true));
        if(is_array($tables)) {
            $t_rowid = 1;
            foreach($tables as $table) {
                $line.= '<tr id='.$t_rowid.'><div>';
                $line.= '<td><input type="text" required="required" name="line['.$table->inputChecksum.'][tableName]" value="'.$table->table.'"></td>';
                $line.= '<input type="hidden" value="'.$this->srlid.'" name="line['.$table->inputChecksum.'][srlid]">';
                $line.= '<td><input type="text" required="required" name="line['.$table->inputChecksum.'][keyColumn]" value="'.$table->keyColumn.'"></td>';
                $line.= '<td><input type="text" required="required" name="line['.$table->inputChecksum.'][displayedColumn]" value="'.$table->displayedColumn.'"></td>';
                $line.= '<td><textarea name="line['.$table->inputChecksum.'][whereClause]">'.$table->whereClause.'</textarea></td>';
                $line.='</div></tr>';
                $t_rowid++;
            }
        }
        return $line;
    }

}