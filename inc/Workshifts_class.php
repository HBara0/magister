<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 * 
 * A class to handle Workshifts
 * $id: Workshifts.php
 * Created:        @zaher.reda    Feb 12, 2014 | 12:37:30 PM
 * Last Update:    @zaher.reda    Feb 12, 2014 | 12:37:30 PM
 */

class Workshifts {
    private $workshift = array();

    public function __construct($id, $simple = FALSE) {
        if(empty($id)) {
            return false;
        }
        $this->read($id, $simple);
    }

    private function read($id, $simple = FALSE) {
        global $db;

        $query_select = 'wsid';
        if($simple == false) {
            $query_select = '*';
        }
        $this->workshift = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'workshifts WHERE wsid='.intval($id)));
    }

    public function get_weekdays() {
        if(!empty($this->workshift['weekDays'])) {
            $weekdays = unserialize($this->workshift['weekDays']);
            if(is_array($weekdays)) {
                foreach($weekdays as $day) {
                    $this->workshift['weekDays_output'] .= $comma.get_day_name($day, 'letters');
                    $comma = ', ';
                }
                unset($weekdays);
                return $this->workshift['weekDays_output'];
            }
        }
    }

    public function get_dutyhours() {
        return $this->workshift['onDutyHour'].':'.$this->workshift['onDutyMinutes'].' - '.$this->workshift['offDutyHour'].':'.$this->workshift['offDutyMinutes'];
    }

    public function get() {
        return $this->workshift;
    }

}
?>
