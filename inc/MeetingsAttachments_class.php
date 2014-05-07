<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: MeetingsAttachments.php
 * Created:        @tony.assaad    Mar 24, 2014 | 2:23:50 PM
 * Last Update:    @tony.assaad    Mar 24, 2014 | 2:23:50 PM
 */

/**
 * Description of MeetingsAttachments
 *
 * @author tony.assaad
 */
class MeetingsAttachments {
    private $attachment = array();
    private $errorcode = 0;
    private $attachment_upload = null;

    const attachments_path = './uploads/meetings';

    public function __construct($id = '', $simple = false) {
        if(isset($id) && !empty($id)) {
            $this->attachment = $this->read($id, $simple);
        }
    }

    private function read($id, $simple = false) {
        global $db;
        $query_select = '*';
        if($simple == true) {
            $query_select = 'mattid, title, filename, type, size';
        }

        return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."meetings_attachments WHERE mattid=".$db->escape_string($id)));
    }

    public function add($attachment, $mtid) {
        global $db, $core;

        $sanitize_fields = array('title', 'filename', 'filesize', 'filetype');
        foreach($sanitize_fields as $val) {
            $attachment[$val] = $core->sanitize_inputs($attachment[$val], array('removetags' => true));
        }

        $attachment_upload['attachments'] = $attachment;
        $upload_status = $this->upload($attachment_upload);
        if($upload_status != 4) {
            return false;
        }
        $attachment = $this->attachment_upload[0];
        $attachment['filename'] = $attachment['name'];
        $attachment['title'] = $attachment['originalname'];
        $attachment['mtid'] = $mtid;
        $attachment['createdBy'] = $core->user['uid'];
        $attachment['createdOn'] = TIME_NOW;
        unset($attachment['tmp_name'], $attachment['error'], $attachment['originalname'], $attachment['name'], $attachment['extension']);
        $query = $db->insert_query('meetings_attachments', $attachment);

        if($query) {
            $this->errorcode = 0;
            return true;
        }
    }

    public function upload($attachement) {
        $upload_param['upload_allowed_types'] = array('image/jpeg', 'image/gif', 'image/png', 'application/zip', 'application/pdf', 'application/x-pdf', 'application/msword', 'application/vnd.ms-powerpoint', 'text/plain', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation');
        $upload_obj = new Uploader('attachments', $attachement, $upload_param['upload_allowed_types'], 'putfile', 5242880, 1, 1); //5242880 bytes = 5 MB (1024);

        $upload_obj->set_upload_path(self::attachments_path);
        $upload_obj->process_file();
        $this->attachment_upload = $upload_obj->get_filesinfo();

        return $upload_obj->get_status();
    }

    public function delete() {
        global $db;
        if(!empty($this->attachment['mattid'])) {
            $query = $db->delete_query('meetings_attachments', 'mattid='.$this->attachment['mattid']);
            if($query) {
                return true;
            }
        }
    }

    public function download() {
        return new Download('meetings_attachments', 'filename', array('mattid' => $this->attachment['mattid']), self::attachments_path);
    }

    public function get_createdby() {
        return new Users($this->attachment['createdBy']);
    }

    public function get_modifiedby() {
        return new Users($this->attachment['modifiedBy']);
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

    public function get() {
        return $this->attachment;
    }

}
?>
