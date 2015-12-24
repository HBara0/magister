<?php
/* -------Definiton-START-------- */

class FacilityMgmtReservations extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'fmrid';
    const TABLE_NAME = 'facilitymgmt_reservations';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = '';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';

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
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        $this->notify_reservations('create');
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        if(empty($data['reservedBy'])) {
            $data['reservedBy'] = $core->user['uid'];
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
        $this->notify_reservations('create');
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
            if($status == 'create') {
                $email_subject = $lang->sprint($lang->reservationcreation_subject, $user->get_displayname(), $facility->getfulladdress(), $affiliate->get_displayname());
                $email_message = $lang->sprint($lang->reservationcreation_message, $facility->getfulladdress(), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->fromDate), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->toDate), $user->get_displayname(), $this->purpose);
            }
            else if($status == 'delete') {
                $email_subject = $lang->sprint($lang->reservationdeletion_subject, $facility->getfulladdress(), $user->get_displayname(), $affiliate->get_displayname());
                $email_message = $lang->sprint($lang->reservationdeletion_message, $facility->getfulladdress(), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->fromDate), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->toDate), $user->get_displayname(), $this->purpose);
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

}