<?php
/* -------Definiton-START-------- */

class FacilityMgmtReservations extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'fmrid';
    const TABLE_NAME = 'facilitymgmt_reservations';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'mtid,fmfid,fromDate,toDate';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';
    const REQUIRED_ATTRS = 'fmfid,fromDate,toDate';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        if(empty($data['reservedBy'])) {
            $data['reservedBy'] = $core->user['uid'];
        }
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 5;
            return $this;
        }
        $table_array = array(
                'fmfid' => $data['fmfid'],
                'fromDate' => $data['fromDate'],
                'toDate' => $data['toDate'],
                'reservedBy' => $data['reservedBy'],
                'purpose' => $data['purpose'],
                'mtid' => $data['mtid'],
                'status' => intval($data['status']),
        );

        $this->data = $table_array;
        if(isset($table_array['mtid']) && !empty($table_array['mtid'])) {
            $pastreservations = self::get_data(array('mtid' => intval($table_array['mtid'])), array('returnarray' => true));
            if(is_array($pastreservations)) {
                foreach($pastreservations as $pastreservation) {
                    $pastreservation->delete();
                }
            }
        }
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        //  $this->notify_reservations('create');
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        if(empty($data['reservedBy'])) {
            $data['reservedBy'] = $core->user['uid'];
        }
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 5;
            return $this;
        }
        if(is_array($data)) {
            $update_array['fmfid'] = $data['fmfid'];
            $update_array['fromDate'] = $data['fromDate'];
            $update_array['toDate'] = $data['toDate'];
            $update_array['reservedBy'] = $data['reservedBy'];
            $update_array['purpose'] = $data['purpose'];
            $update_array['mtid'] = $data['mtid'];
            $update_array['status'] = $data['status'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        // $this->notify_reservations('update');
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    /* -------GETTER FUNCTIONS-START-------- */
    public function get_reservedBy() {
        return new Users($this->data['reservedBy']);
    }

    /* -------GETTER FUNCTIONS-END-------- */
    public function get_meeting() {
        return new Meetings($this->data['mtid']);
    }

    public function get_facility() {
        return new FacilityMgmtFacilities($this->data['fmfid']);
    }

    public function notify_reservations($status) {
        global $lang, $core;
        $lang->load('facilitymgmt_meta');
        $facility = $this->get_facility();
        if(is_object($facility)) {
            $affiliate = $facility->get_affiliate();
            if(is_object($affiliate)) {
                if(!empty($affiliate->mailingList)) {
                    $email_to = $affiliate->mailingList;
                }
                else if(!empty($affiliate->altMailingList)) {
                    $email_to = $affiliate->altMailingList;
                }
            }
        }

        if(!empty($email_to)) {
            $user = new Users($this->reservedBy);
            if(!empty($this->purpose)) {
                $purpose_obj = FacilityManagementReservePurpose::get_data(array('alias' => $this->data['purpose']), array('returnarray' => false));
                if(is_object($purpose_obj)) {
                    $purpose = $lang->purpose.' : '.$purpose_obj->get_displayname();
                }
            }
            if($status == 'create') {
                $email_subject = $lang->sprint($lang->reservationcreation_subject, $user->get_displayname(), $facility->getfulladdress(), $affiliate->get_displayname());
                $email_message = $lang->sprint($lang->reservationcreation_message, $facility->getfulladdress(), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->fromDate), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->toDate), $user->get_displayname(), $purpose);
            }
            else if($status == 'delete') {
                $email_subject = $lang->sprint($lang->reservationdeletion_subject, $facility->getfulladdress(), $user->get_displayname(), $affiliate->get_displayname());
                $email_message = $lang->sprint($lang->reservationdeletion_message, $facility->getfulladdress(), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->fromDate), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->toDate), $user->get_displayname(), $purpose);
            }
            else if($status == 'update') {
                $email_subject = $lang->sprint($lang->reservationupdate_subject, $facility->getfulladdress(), $user->get_displayname(), $affiliate->get_displayname());
                $email_message = $lang->sprint($lang->reservationupdate_message, $facility->getfulladdress(), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->fromDate), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->toDate), $user->get_displayname(), $purpose);
            }
            $email_data = array(
                    'from_email' => $core->settings['maileremail'],
                    'from' => 'OCOS Mailer',
                    'subject' => $email_subject,
                    'message' => $email_message,
                    'to' => $email_to,
            );
            $mail = new Mailer($email_data, 'php');
        }
    }

    public function delete() {
        global $db;
        if(empty($this->data[static::PRIMARY_KEY]) && empty($this->data['inputChecksum'])) {
            return false;
        }
        elseif(empty($this->data[static::PRIMARY_KEY]) && !empty($this->data['inputChecksum'])) {
            $query = $db->delete_query(static::TABLE_NAME, 'inputChecksum="'.$db->escape_string($this->data['inputChecksum']).'"');
        }
        else {
            $query = $db->delete_query(static::TABLE_NAME, static::PRIMARY_KEY.'='.intval($this->data[static::PRIMARY_KEY]));
        }
        if($query) {
            $this->notify_reservations('delete');
            return true;
        }
        return false;
    }

    public function validate_requiredfields($data) {
        global $errorhandler;
        $required_fields = self::REQUIRED_ATTRS;
        if(!empty($required_fields)) {
            $required_fields = explode(',', $required_fields);
            if(is_array($required_fields) && is_array($data)) {
                foreach($required_fields as $field) {
                    if(!isset($data[$field]) || empty($data[$field])) {
                        $errorhandler->record('Required fields', $field);
                        return false;
                    }
                }
            }
        }
        if($data['fromDate'] > $data['toDate']) {
            $errorhandler->record('Wrong dates', $field);
            return false;
        }

        return true;
    }

}