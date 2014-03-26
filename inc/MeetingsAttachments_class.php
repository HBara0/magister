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
	private $meetingattachments = array();
	private $errorcode = 0;

	public function __construct($id = '', $simple = false) {
		if(isset($id) && !empty($id)) {
			$this->attachments = $this->read($id, $simple);
		}
	}

	private function read($id, $simple = false) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'mattid, title, filename';
		}

		return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."meetings_attachments WHERE mattid=".$db->escape_string($id)));
	}

	public function add($attachments, $mtid) {
		global $db, $core;
		if(is_array($attachments)) {
			$sanitize_fields = array('title', 'filename', 'filesize', 'filetype');
			foreach($sanitize_fields as $val) {
				$attachment[$val] = $core->sanitize_inputs($attachment[$val], array('removetags' => true));
			}

			foreach($attachments as $key => $attachment) {
				unset($attachment['tmp_name'], $attachment['error']);

				foreach($attachment as $field => $attachmentval) {
					foreach($attachmentval as $id => $val) {
						$attachements_data[$id][$field] = $val;
					}
				}
			}
			if(is_array($attachements_data)) {
				foreach($attachements_data as $value) {
					$value['mtid'] = $mtid;
					$value['createdBy'] = $core->user['uid'];
					$value['createdOn'] = TIME_NOW;
					$query = $db->insert_query('meetings_attachments', $value);
				}
			}
			if($query) {
				$this->errorcode = 0;
				return TRUE;
			}
		}
	}

	public function upload($attachements) {
		$upload_param['upload_allowed_types'] = array('image/jpeg', 'image/gif', 'image/png', 'application/zip', 'application/pdf', 'application/x-pdf', 'application/msword', 'application/vnd.ms-powerpoint', 'text/plain', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation');
		$upload_obj = new Uploader('attachments', $attachements, $upload_param['upload_allowed_types'], 'putfile', 5242880, 1, 1); //5242880 bytes = 5 MB (1024);

		$attachments_path = './uploads/meetings';
		$upload_obj->set_upload_path($attachments_path);
		$upload_obj->process_file();
		$attachments = $upload_obj->get_filesinfo();

		if($upload_obj->get_status() != 4) {
			$this->uploadstatus = $upload_obj->parse_status($upload_obj->get_status());
		}
		return $this->uploadstatus;
	}

	public function delete() {
		global $db;
		if(!empty($this->attachments['mattid'])) {
			$query = $db->delete_query('meetings_attachments', 'mattid='.$this->attachments['mattid']);
			if($query) {
				return true;
			}
		}
	}

	public function get_createdby() {
		return new Users($this->attachments['createdBy']);
	}

	public function get_modifiedby() {
		return new Users($this->attachments['modifiedBy']);
	}

	public function get_errorcode() {
		return $this->errorcode;
	}

	public function get() {
		return $this->attachments;
	}

}
?>
