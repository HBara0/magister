<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Surveys Class
 * $id: Surveys_class.php
 * Created:		@zaher.reda		April 20, 2012 | 10:53 AM
 * Last Update: @tony.assaad	January 10, 2013 | 10:33 AM
 */

class Surveys {
    private $survey = array();
    private $status = 0; //0=No errors;1=Subject missing;2=Entry exists;3=Error saving;4=validation violation

    /* public $stid = 0; */
    public function __construct($id = '', $simple = false) {
        global $core;

        if(isset($id) && !empty($id)) {
            $this->survey = $this->read_survey($id, $simple);
        }
    }

    public function create_survey(array $data) {
        global $db, $log, $core;

        if(is_empty($data['subject'], $data['stid'], $data['category'])) {
            $this->status = 1;
            return false;
        }

        /* Check if survery with same subject created by the same user exists */
        if(value_exists('surveys', 'subject', $data['subject'], 'createdBy='.$db->escape_string($core->user['uid']))) {
            $this->status = 2;
            return false;
        }

        if($data['isExternal'] == 1) {
            if(empty($data['externalinvitations'])) {
                $this->status = 4;
                return false;
            }
        }
        else {
            if($data['isPublicFill'] == 0 && (empty($data['invitations']) || empty($data['invitations']))) {
                $this->status = 4;
                return false;
            }
        }
        /*
          Identifier is a unique key represented to users as reference to the survey.
          While user's input reference, if any, is their own reference
         */
        $data['identifier'] = substr(md5(uniqid(microtime())), 1, 10);
        $data['subject'] = ucwords(strtolower($data['subject']));

        /* Closing date can be empty, meaning survey doesn't expire */
        if(isset($data['closingDate']) && !empty($data['closingDate'])) {
            $data['closingDate'] = strtotime($data['closingDate']);
        }
        $data['dateCreated'] = TIME_NOW;
        $data['description'] = preg_replace("/<br \/>/i", "\n", $data['description']);
        $data['createdBy'] = $core->user['uid'];

        unset($data['action'], $data['module']);

        /*  Sanitize inputs - START */
        $data['subject'] = $core->sanitize_inputs($data['subject'], array('removetags' => true));
        $data['description'] = $core->sanitize_inputs($data['description'], array('method' => 'striponly', 'removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><img><cite><small>'));
        $data['customInvitationSubject'] = $core->sanitize_inputs($data['customInvitationSubject'], array('removetags' => true));
        //$data['customInvitationBody'] = $core->sanitize_inputs($data['customInvitationBody'], array('method' => 'striponly', 'removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><img><cite><small>'));
        /*  Sanitize inputs - END */

        $this->survey = $data;

        if($this->survey['isExternal'] == 0) {
            /* Make random selection of invitees if needed */
            $this->randomly_select_invitations($data['inviteesnumber']);
        }
        else {
            if($this->validate_external_invitations() == false) {
                $this->status = 5;
                return false;
            }
        }

        unset($data['customInvitation'], $data['invitations'], $data['associations'], $data['inviteesnumber'], $data['externalinvitations']);
        $surveytemplate = new SurveysTemplates($data['stid'], false);
        if($surveytemplate->isQuiz == 1) {
            $data['isQuiz'] = 1;
            $data['total'] = $surveytemplate->total;
        }
        $query = $db->insert_query('surveys', $data);
        if($query) {
            $this->status = 0;
            $this->survey['sid'] = $db->last_id();

            if($this->survey['isPublicFill'] == 0) {
                if((isset($this->survey['invitations']) && !empty($this->survey['invitations'])) || (isset($this->survey['externalinvitations']) && !empty($this->survey['externalinvitations']))) {
                    $this->set_invitations();
                    $this->send_invitations();
                }
            }
            $this->set_associations();
            $log->record($this->survey['sid']);
            return true;
        }
        else {
            $this->status = 3;
            return false;
        }
    }

    public function update_survey(array $data) {
        global $db, $log, $core;

        if(is_empty($data['subject'], $data['category'])) {
            $this->status = 1;
            return false;
        }
        /* Check if survery with same subject created by the same user exists */
        if(value_exists('surveys', 'subject', $data['subject'], 'createdBy='.$db->escape_string($core->user['uid'].' AND sid <>'.$data['sid']))) {
            $this->status = 2;
            return false;
        }
        $fields = array('reference', 'subject', 'description', 'closingDate', 'category', 'isPublicResults', 'closingDate');
        foreach($fields as $field) {
            switch($field) {
                case 'subject':
                    $survey_data[$field] = $core->sanitize_inputs(ucwords(strtolower($data[$field])), array('removetags' => true));
                    break;
                case 'description':
                    $survey_data[$field] = preg_replace("/<br \/>/i", "\n", $data[$field]);
                    $survey_data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true));
                    break;
                case 'modifiedOn':
                    $survey_data[$field] = TIME_NOW;
                    break;
                case 'modifiedBy':
                    $survey_data[$field] = $core->user['uid'];
                    break;
                default:
                    $survey_data[$field] = $data[$field];
                    break;
            }
        }
        $this->survey = $data;
        $query = $db->update_query('surveys', $survey_data, 'sid='.$data['sid']);
        if($query) {
            $this->status = 0;
            $this->survey['sid'] = $data['sid'];
            foreach($this->survey['associations'] as $key => $val) {
                if(empty($val)) {
                    $surv_association = SurveyAssociations::get_data(array('sid' => $this->survey['sid'], 'attr' => $key));
                    if(is_object($surv_association)) {
                        $surv_association->delete();
                    }
                    continue;
                }
                $association_data['sid'] = $this->survey['sid'];
                $association_data['attr'] = $key;
                $association_data['id'] = $core->sanitize_inputs($val);
                $surv_association = new SurveyAssociations();
                $surv_association->set($association_data);
                $surv_association->save();
                $this->errorcode = $surv_association->errorcode;
                switch($this->errorcode) {
                    case 0:
                        continue;
                    case 1:
                        return;
                };
            }
            return $this;
        }
        else {
            $this->status = 3;
            return $this;
        }
    }

    public function send_additional_invitations($data) {
        $survey = $this->get_survey();
        if($survey['isExternal'] == 0) {
            $this->survey['invitations'] = $data['invitations'];
        }
        elseif($survey['isExternal'] == 1) {
            $this->survey['externalinvitations'] = $data['externalinvitations'];
            $this->validate_external_invitations();
        }
        $this->set_invitations();
        $this->send_invitations($this->survey['newinvitations']);
    }

    private function validate_external_invitations() {
        global $core, $errorhandler;
        if(isset($this->survey['externalinvitations'])) {
            $external_invitations = preg_split("/[\n;]+/", $this->survey['externalinvitations']);
            $this->survey['externalinvitations'] = array();  /* reset the array before cleaning */

            foreach($external_invitations as $key => $externalinvitee) {
                $externalinvitee = trim($externalinvitee);
                if(empty($externalinvitee)) {
                    continue;
                }

                $new_invitation['invitee'] = $core->sanitize_email($externalinvitee);
                $new_invitation['invitee'] = $core->validate_email($externalinvitee);
                if($new_invitation['invitee'] == false) {
                    $errorhandler->record('invalidemailaddress', $externalinvitee);
                    return false;
                }
                $previnvitations = $this->get_invitations();
                if(is_array($previnvitations)) {
                    $previnvitations = array_keys($previnvitations);
                }
                if(!is_array($previnvitations)) {
                    $this->survey['externalinvitations'][] = $new_invitation['invitee'];
                }
                elseif(!in_array($new_invitation['invitee'], $previnvitations)) {
                    $this->survey['externalinvitations'][] = $new_invitation['invitee'];
                }
            }
        }
        return true;
    }

    public function create_survey_template(array $data) {
        global $db, $core, $log;
        $cache = new Cache();
        $totalquestions = 0;
        unset($data['action'], $data['module']); /* here we destroy the  action and module from the data ARRAY to avoid insert module name and action in the DB  */
        if(empty($data['title'])) {
            $this->status = 1;
            return false;
        }
        /* Check if template with same name created by any user */
        if(value_exists('surveys_templates', 'title', $data['title'])) {
            $this->status = 2;
            return false;
        }

        /* Validate that data is complete before creating anything - START */
        foreach($core->input['section'] as $key => $section) {
            if(!is_array($section)) {
                unset($core->input['section'][$key]);
                continue;
            }

            if(empty($section['title'])) {
                $this->status = 1;
                return false;
            }

            /* Check if same section title has been used in the same template */
            if($cache->incache('sectiontitles', $section['title'])) {
                $this->status = 4;
                return false;
            }
            else {
                $cache->add('sectiontitles', $section['title'], $key);
                foreach($section['questions'] as $stqid => $question) {
                    if(count($section['questions']) == 1) {
                        if(empty($question['question'])) {
                            unset($core->input['section'][$key]);
                            break;
                        }
                    }
                    elseif(empty($question['question'])) {
                        unset($core->input['section'][$key]['questions'][$stqid]);
                        continue;
                    }
                    if(is_empty($question['question'], $question['type'])) {
                        return false;
                    }
                    if($cache->incache('questiontitles', $question['question'])) {
                        $this->status = 5;
                        return false;
                    }
                    else {
                        $cache->add('questiontitles', $question['question'], $stqid);
                        if(!$cache->iscached('hasChoices', $question['type'])) {
                            $question['hasChoices'] = $db->fetch_field($db->query('SELECT hasChoices FROM '.Tprefix.'surveys_questiontypes WHERE sqtid='.$db->escape_string($question['type'])), 'hasChoices');
                            $cache->add('hasChoices', $question['hasChoices'], $question['type']);
                        }
                        else {
                            $question['hasChoices'] = $cache->data['hasChoices'][$question['type']];
                        }

                        if($question['hasChoices'] == 1) {
                            $totalquestions++;
                            if(empty($question['choices'])) {
                                $this->status = 1;
                                return false;
                            }
                            else { /* Validate choices if meet the pattern and  has value  before insert */

                                /* Split the choices value by ";" */
                                if(is_array($question['choices'])) {
                                    foreach($question['choices'] as $key => $choice) {
                                        if(is_array($choice)) {
                                            if(empty($choice['choice'])) {
                                                $this->status = 6;
                                                return false;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        /* Validate that data is complete before creating anything - END */
        $newsurveys_template = array(
                'dateCreated' => TIME_NOW,
                'category' => $core->input['category'],
                'isPublic' => $core->input['isPublic'],
                'title' => $core->sanitize_inputs($core->input['title']),
                'forceAnonymousFilling' => $core->input['forceAnonymousFilling'],
                'createdBy' => $core->user['uid'],
                'isQuiz' => intval($core->input['isQuiz']));
        if($newsurveys_template['isQuiz'] == 1) {
            $newsurveys_template['total'] = $totalquestions;
        }
        $query = $db->insert_query('surveys_templates', $newsurveys_template);

        if($query) {
            $stid = $db->last_id();
            $sequence = 1;
            foreach($core->input['section'] as $key => $section) {
                $newsurveys_section = array(
                        'stid' => $stid,
                        'title' => $core->sanitize_inputs(trim($section['title'])));

                $section_query = $db->insert_query('surveys_templates_sections', $newsurveys_section);
                if($section_query) {
                    $stsid = $db->last_id();
                    foreach($section['questions'] as $key => $question) {
                        $question['stsid'] = $stsid;
                        $question['sequence'] = $sequence;
                        if(isset($question['choices']) && is_array($question['choices'])) {
                            $question_choices = $question['choices'];
                        }

                        if(!empty($question['commentsFieldTitle'])) {
                            $question['hasCommentsField'] = 1;
                            $question['commentsFieldType'] = 'textarea';
                        }
                        unset($question['choices']);


                        $query_question = $db->insert_query('surveys_templates_questions', $question);
                        if($query_question) {
                            $stqid = $db->last_id();

                            if(!empty($question_choices) && is_array($question_choices)) {
                                /* Split the question choices by "\n"  */
                                /* Split the choices value by ";"  */
                                foreach($question_choices as $key => $choice) {
                                    if(!empty($choice['choice'])) {
                                        $choice['stqid'] = $stqid;
                                        $query_choice = $db->insert_query('surveys_templates_questions_choices', $choice);
                                    }
                                }
                            }
                        }
                        $sequence++;
                    }
                }
            }
            $log->record('createsurveytemplate', $stid);
            $this->status = 0;
            $this->stid = $stid;
            return true;
        }
        else {
            $this->status = 3;
            return false;
        }
    }

    private function read_survey($id, $simple = false) {
        global $db;

        $query_select = '*';
        if($simple == true) {
            $query_select = 'sid, identifier';
        }

        return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."surveys WHERE identifier='".$db->escape_string($id)."'"));
    }

    public function get_survey() {
        return $this->survey;
    }

    public function get_template() {
        if(isset($this->survey['stid']) && !empty($this->survey['stid'])) {
            $template = new SurveysTemplates($this->survey['stid']);
            if(!is_object($template)) {
                return false;
            }
            return $template;
        }
        return false;
    }

    public function get() {
        return $this->survey;
    }

    /*
     * Get all surveys that a user can view
     * @return	array	$surveys	Array containing sid & identifier for each
     */
    public function get_user_surveys() {
        global $db, $core;

        $sort_query = 'dateCreated DESC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = $core->input['sortby'].' '.$core->input['order'];
        }

        $query = $db->query("SELECT s.*, s.dateCreated AS date, sc.title AS categorytitle
							FROM ".Tprefix."surveys s
							JOIN ".Tprefix."surveys_categories sc ON (s.category=sc.scid)
							WHERE s.createdBy={$core->user[uid]} OR s.sid IN (SELECT sid FROM ".Tprefix."surveys_sharedwith WHERE uid={$core->user[uid]}) OR s.sid IN (SELECT sid FROM ".Tprefix."surveys_associations WHERE attr='uid' AND id={$core->user[uid]}) OR s.isPublicFill=1 OR s.sid IN (SELECT sid FROM ".Tprefix."surveys_invitations WHERE invitee={$core->user[uid]})
							ORDER BY {$sort_query}");

        if($db->num_rows($query) > 0) {
            while($survey = $db->fetch_assoc($query)) {
                $surveys[$survey['sid']] = $survey;
            }
            return $surveys;
        }
        return false;
    }

    public function get_responseidentifier($sid, $uid) {
        global $db;

        return $db->fetch_field($db->query("SELECT DISTINCT(identifier)
							FROM ".Tprefix."surveys_responses
				 			WHERE sid={$sid} AND invitee={$uid}"), 'identifier');
    }

    public function get_all_responses() {
        global $db;

        $query = $db->query("SELECT * FROM ".Tprefix."surveys_responses WHERE sid='".$this->survey['sid']."'");
        while($response = $db->fetch_assoc($query)) {
            $this->survey['responses'][$response['identifier']][$response['srid']] = $response;
        }
        return $this->survey['responses'];
    }

    public function get_single_responses($identifier) {
        global $db, $core;
//AND sr.uid={$core->user[uid]}
        $query = $db->query("SELECT s.identifier AS survey_identifier, sr.*, sqt.hasChoices, sqt.hasMultiAnswers
						FROM ".Tprefix."surveys_responses sr
						JOIN ".Tprefix."surveys s ON (s.sid=sr.sid)
						JOIN ".Tprefix."surveys_templates_questions stq ON (stq.stqid=sr.stqid)
						JOIN ".Tprefix."surveys_questiontypes sqt ON (stq.type=sqt.sqtid)
						WHERE sr.identifier='".$db->escape_string($identifier)."'  AND (sr.invitee={$core->user[uid]} OR {$core->user[uid]} = s.createdBy)
						ORDER BY stqid ASC");

        if($db->num_rows($query) > 0) {
            while($response = $db->fetch_assoc($query)) {
                $survey_identifier = $response['survey_identifier'];
                if($response['hasChoices'] == 1) {
                    $response['choice'] = $db->fetch_field($db->query("SELECT choice FROM ".Tprefix."surveys_templates_questions_choices WHERE stqid='{$response[stqid]}' AND stqcid='{$response[response]}'"), 'choice');
                    if(empty($response['choice'])) {
                        $response['choice'] = $db->fetch_field($db->query("SELECT choice FROM ".Tprefix."surveys_templates_questions_choices WHERE stqcid='{$response[comments]}'"), 'choice');
                    }
                }

                if($response['hasMultiAnswers'] == 0) {
                    $responses[$response['stqid']] = $response;
                }
                else {
                    $responses[$response['stqid']][$response['srid']] = $response;
                }
            }

            $this->survey = $this->read_survey($survey_identifier);
            return $responses;
        }
        else {
            return false;
        }
    }

    public function get_survey_distinct_responses($identifier = '', array $order = array()) {
        global $db;
        if(empty($identifier) && isset($this->survey['identifier'])) {
            $identifier = $this->survey['identifier'];
        }
        elseif(empty($identifier) && !isset($this->survey['identifier'])) {
            return false;
        }

        $sort_query = 'identifier DESC';
        if(isset($order['sortby'], $order['order']) && !is_empty($order['sortby'], $order['order'])) {
            $sort_query = $db->escape_string($order['sortby']).' '.$db->escape_string($order['order']);
        }

        if($this->survey['isExternal'] == 1) {
            $query = $db->query("SELECT DISTINCT(sr.identifier), time, si.invitee AS respondant,si.passed
					FROM ".Tprefix."surveys s
					JOIN ".Tprefix."surveys_responses sr ON (sr.sid=s.sid)
					JOIN ".Tprefix."surveys_invitations si ON (si.invitee=sr.invitee)
					WHERE s.identifier='".$db->escape_string($identifier)."'
					ORDER BY {$sort_query}");
        }
        else {
            $query = $db->query("SELECT DISTINCT(sr.identifier), time, sr.invitee AS uid, displayName AS respondant,si.passed
								FROM ".Tprefix."surveys s
								JOIN ".Tprefix."surveys_responses sr ON (sr.sid=s.sid)
								JOIN ".Tprefix."users u ON (u.uid=sr.invitee)
                                                                JOIN ".Tprefix."surveys_invitations si ON (si.invitee=sr.invitee)
								WHERE s.identifier='".$db->escape_string($identifier)."'
								ORDER BY {$sort_query}");
        }
        while($survey_response = $db->fetch_assoc($query)) {
            $survey_responses[$survey_response['identifier']] = $survey_response;
        }

        return $survey_responses;
    }

    public function get_pendingresposes($identifier = '') {
        global $db;
//        if($this->survey['isExternal'] == 1) {
//            $query = $db->query("SELECT DISTINCT(sr.identifier) AS respondant
//					FROM ".Tprefix."surveys_invitations si WHERE invitee NOT IN(SELECT identifier
//					FROM ".Tprefix."surveys_responses WHERE sid=".$sid.") WHERE sid=".$sid);
//        }
        $query = $db->query("SELECT siid,invitee,identifier FROM ".Tprefix."surveys_invitations WHERE isDone IS NULL AND sid=".$this->survey['sid']);
        while($inviteerow = $db->fetch_assoc($query)) {
            $pending_responses[$inviteerow['siid']]['invitee'] = $inviteerow['invitee'];
            $pending_responses[$inviteerow['siid']]['identifier'] = $inviteerow['identifier'];
        }
        return $pending_responses;
    }

    public function parse_response(array $response, array $question, $isquiz = 0) {
        if(is_empty($response, $question)) {
            return false;
        }

        return $this->parse_question($question, false, $response, $isquiz);
    }

    public function get_responses_stats($ignore_zero = false) {
        global $db;

        if(empty($identifier)) {
            $identifier = $this->survey['identifier'];
        }
        else {
            $identifier = $identifier;
        }

        $query = $db->query("SELECT s.identifier AS survey_identifier, stq.sequence, s.subject, stqc.*, sr.*, stq.question, sqt.hasChoices, sqt.hasMultiAnswers, sqt.isQuantitative
						FROM ".Tprefix."surveys_responses sr
						JOIN ".Tprefix."surveys s ON (s.sid=sr.sid)
						JOIN ".Tprefix."surveys_templates_questions stq ON (stq.stqid=sr.stqid)
						JOIN ".Tprefix."surveys_questiontypes sqt ON (stq.type=sqt.sqtid)
						JOIN ".Tprefix."surveys_templates_questions_choices stqc ON (stqc.stqcid=sr.response)
						WHERE s.identifier='".$db->escape_string($identifier)."' AND sqt.isQuantitative=1 AND sqt.hasChoices=1
						ORDER BY stq.sequence");

        while($responses_stat = $db->fetch_assoc($query)) {
            $responses_stats[$responses_stat['stqid']]['number'] = $responses_stat['sequence'];
            $responses_stats[$responses_stat['stqid']]['title'] = $responses_stat['question'];
            $responses_stats[$responses_stat['stqid']]['choices']['choice'][$responses_stat['stqcid']] = $responses_stat['choice'];
            if(strlen($responses_stat['choice']) > 40) {
                $responses_stats[$responses_stat['stqid']]['choices']['choice'][$responses_stat['stqcid']] = $responses_stat['value'];
            }
            $responses_stats[$responses_stat['stqid']]['choices']['value'][$responses_stat['stqcid']] = $responses_stat['value'];
            $responses_stats[$responses_stat['stqid']]['choices']['stats'][$responses_stat['stqcid']] ++;
        }
        /* Get choices that were not selected */
        if($ignore_zero == false) {
            if(isset($responses_stats)) {
                foreach($responses_stats as $stqid => $data) {
                    $query2 = $db->query("SELECT * FROM ".Tprefix."surveys_templates_questions_choices WHERE stqid={$stqid}");
                    while($other_choices = $db->fetch_assoc($query2)) {
                        if(!isset($responses_stats[$other_choices['stqid']]['choices']['stats'][$other_choices['stqcid']])) {
                            $responses_stats[$other_choices['stqid']]['choices']['choice'][$other_choices['stqcid']] = $other_choices['choice'];
                            if(strlen($other_choices['choice']) > 40) {
                                $responses_stats[$other_choices['stqid']]['choices']['choice'][$other_choices['stqcid']] = $other_choices['value'];
                            }
                            $responses_stats[$other_choices['stqid']]['choices']['value'][$other_choices['stqcid']] = $other_choices['value'];
                            $responses_stats[$other_choices['stqid']]['choices']['stats'][$other_choices['stqcid']] = 0;
                        }
                    }
                }
            }
        }
        return $responses_stats;
    }

    public function save_responses(array $answers) {
        global $db, $core;

        if(value_exists('surveys_responses', 'sid', $this->survey['sid'], 'invitee='.$core->user['uid'])) {
            $this->status = 2;
            return false;
        }
        $corrects = 0;
        $total = 0;
        if($this->validate_answers($answers['actual'])) {
            $identifier = substr(md5(uniqid(microtime())), 1, 10);
            foreach($answers['actual'] as $id => $value) {
                $total++;
                if(is_array($value)) {
                    foreach($value as $vid => $val) {
                        $answer = 0;
                        $questionchoice = new SurveysTplQChoices(intval($val));
                        if($questionchoice->isAnswer == 1) {
                            $answer = 1;
                            $corrects++;
                        }
                        $this->save_single_response(array('id' => $id, 'value' => $val, 'comments' => $answers['comments'][$id][$vid], 'identifier' => $identifier, 'isCorrect' => $answer));
                    }
                }
                else {
                    $answer = 0;
                    $questionchoice = new SurveysTplQChoices(intval($value));
                    if($questionchoice->isAnswer == 1) {
                        $answer = 1;
                        $corrects++;
                    }
                    $this->save_single_response(array('id' => $id, 'value' => $val, 'comments' => $answers['comments'][$id][$vid], 'identifier' => $identifier, 'isCorrect' => $answer));
                }
            }
            /* Set contribution as done */
            $pass = 0;
            if($corrects > 0 && $corrects > ($this->survey['total'] / 2)) {
                $pass = 1;
            }
            $db->update_query('surveys_invitations', array('total' => $total, 'passed' => $pass, 'score' => $corrects, 'isDone' => 1, 'timeDone' => TIME_NOW), 'invitee='.$core->user['uid'].' AND sid='.$this->survey['sid']);
            $this->status = 0;
            return true;
        }
        else {
            return false;
        }
    }

    private function save_single_response(array $answer) {
        global $db, $core;

        $response = array(
                'sid' => $this->survey['sid'],
                'stqid' => $answer['id'],
                'invitee' => $core->user['uid'],
                'identifier' => $answer['identifier'],
                'response' => $core->sanitize_inputs($answer['value']),
                'comments' => $core->sanitize_inputs($answer['comments']),
                'time' => TIME_NOW,
                'isCorrect' => $answer['isCorrect'],
        );
        $query = $db->insert_query('surveys_responses', $response);
        if($query) {
            $this->status = 0;
            return true;
        }
        $this->status = 3;
        return false;
    }

    /*
     * Check if answers meet the validation requirements
     *
     */
    private function validate_answers(array $answers, $validation_id = '') {
        $validations = $this->get_questions_validations();

        foreach($validations as $id => $validation) {
            if($this->validate_answer($answers[$id], $validations[$id]) == false) {
                return false;
            }
        }
        return true;
    }

    private function validate_answer($answer, $validation) {
        global $core;


        if(!is_array($answer) && ((empty($answer) || !isset($answer)) && $validation['isRequired'] == 1)) {
            $this->status = 1;
            return false;
        }

        if($validation['hasValidation'] == 1 && !empty($answer)) {
            if(is_array($answer)) {
                foreach($answer as $value) {
                    return $this->validate_answer($value, $validation);
                }
            }

            if($validation['validationType'] == 'minchars' && strlen($answer) < $validation['validationCriterion']) {
                $this->status = 4;
                return false;
            }

            if($validation['validationType'] == 'maxchars' && strlen($answer) > $validation['validationCriterion']) {
                $this->status = 4;
                return false;
            }

            if($validation['validationType'] == 'email' && !$core->validate_email($answer)) {
                $this->status = 4;
                return false;
            }

            if($validation['validationType'] == 'numeric' && !is_numeric($answer)) {
                $this->status = 4;
                return false;
            }
        }
        else {
            if(is_array($answer)) {
                foreach($answer as $value) {
                    return $this->validate_answer($value, $validation);
                }
            }
        }
        return true;
    }

    private function parse_validation($question) {
        global $lang;
        switch($question['validationType']) {
            case 'numeric':
                $note = $lang->numbersonly;
                break;
            case 'minchars':
            case 'maxchars':
                $note = $lang->sprint($lang->{$question['validationType']}, $question['validationCriterion']);
                break;
            case 'email':
                $note = $lang->emailonly;
                break;
            default: return false;
        }
        return '<span class="smalltext" style="font-style:italic;">('.$note.')</span>';
    }

    public function set_associations() {
        global $db, $core;

        if(!empty($this->survey['associations'])) {
            foreach($this->survey['associations'] as $key => $val) {
                if(empty($val)) {
                    continue;
                }
                $new_association['sid'] = $this->survey['sid'];
                $new_association['attr'] = $key;
                $new_association['id'] = $core->sanitize_inputs($val);
                $db->insert_query('surveys_associations', $new_association);
            }
        }
    }

    public function set_invitations() {
        global $db;

        if($this->survey['isExternal'] == 0) {
            $temp_invitations = array();
            foreach($this->survey['invitations'] as $group => $invitations) {
                if(is_array($invitations)) {
                    $temp_invitations = array_merge($temp_invitations, $invitations);
                }
                else {
                    array_push($temp_invitations, $invitations);
                }
            }
            $this->survey['invitations'] = array_unique($temp_invitations);
        }
        else {
            $this->survey['invitations'] = $this->survey['externalinvitations'];
        }
        foreach($this->survey['invitations'] as $invitation) {
            $new_invitation['identifier'] = substr(md5(uniqid(microtime())), 0, 10);
            $new_invitation['sid'] = $this->survey['sid'];
            $new_invitation['invitee'] = $invitation;
            $db->insert_query('surveys_invitations', $new_invitation);
            $this->survey['newinvitations'][] = $new_invitation;
        }
    }

    public function send_invitations($newinvitations = array()) {
        global $core, $lang, $template, $log;
        if(isset($newinvitations) && !empty($newinvitations)) {
            $this->survey['invitations'] = $newinvitations;
        }
        else {
//if(!isset($this->survey['invitations'])) {
            $this->survey['invitations'] = $this->get_invitations();
        }
//}
        $lang->load('messages');
        foreach($this->survey['invitations'] as $uid => $invitee) {
            if($this->survey['isExternal'] == 0) {
                $invitations_email = array(
                        'to' => $invitee['email'],
                        'from_email' => $core->settings['maileremail'],
                        'from' => 'OCOS Mailer'
                );
            }
            else {
                $invitations_email = array(
                        'to' => $invitee['invitee'],
                        'from_email' => $core->user['email'],
                        'from' => $core->user['displayName']
                );
            }

            if(isset($this->survey['customInvitationSubject']) && !empty($this->survey['customInvitationSubject'])) {
                $invitations_email['subject'] = $this->survey['customInvitationSubject'];
            }
            else {
                $invitations_email['subject'] = $this->survey['subject'];
            }

            $surveylink = DOMAIN.'/index.php?module=surveys/fill&amp;identifier='.$this->survey['identifier'];
            if($this->survey['isExternal'] == 1) {
                $surveylink = 'http://www.orkila.com/surveys/'.$this->survey['identifier'].'/'.$invitee['identifier'];
            }

            if(isset($this->survey['customInvitation']) && !empty($this->survey['customInvitation'])) {
                $invitations_email['message'] = $this->survey['customInvitationBody'];

                if(strstr($invitations_email['message'], '{link}')) {
                    $invitations_email['message'] = str_replace('{link}', $surveylink, $invitations_email['message']);
                }
                else {
                    $invitations_email['message'] .= '<br />'.$lang->sprint($lang->accesssurveylink, $surveylink);
                }
                $surv_createsurv_invlayt_body = $invitations_email['message'];
            }
            else {
                fix_newline($this->survey['description']);
                if($this->survey['isExternal'] == 1) {
                    $invitee['displayName'] = '';
                }
                $invitations_email['message'] = $lang->sprint($lang->surveys_invitation_message, $invitee['displayName'], $this->survey['subject'], $this->survey['description'], $surveylink);
                eval("\$surv_createsurv_invlayt_body = \"".$template->get('surveys_createsurvey_invitationlayout_body')."\";");
            }

            eval("\$invitations_email[message] = \"".$template->get('surveys_createsurvey_invitationlayout')."\";");

            $mail = new Mailer($invitations_email, 'php');
            if($mail->get_status() === true) {
                $log->record('sendinvitations', array('to' => $invitation_data['email']));
//return true;
            }
        }
        return true;
    }

    public function get_invitations() {
        global $db;

        if(empty($this->survey['sid'])) {
            return null;
        }
        if($this->survey['isExternal']) {
            $invitations_query = $db->query("SELECT si.*
							FROM ".Tprefix."surveys_invitations si
							JOIN ".Tprefix."surveys s ON (s.sid=si.sid)
							WHERE si.sid={$this->survey[sid]}
							ORDER BY s.sid DESC");
        }
        else {
            $invitations_query = $db->query("SELECT si.*, u.uid, u.email, u.displayName
							FROM ".Tprefix."surveys_invitations si
							JOIN ".Tprefix."users u ON (u.uid=si.invitee)
							JOIN ".Tprefix."surveys s ON (s.sid=si.sid)
							WHERE si.sid={$this->survey[sid]}
							ORDER BY s.sid DESC");
        }
        while($invitation = $db->fetch_assoc($invitations_query)) {
            $invitations[$invitation['invitee']] = $invitation;
        }
        return $invitations;
    }

    public function get_associations() {
        global $db;

        $options = array('uid' => array('tablename' => 'users', 'attrselect' => 'displayname', 'attrwhere' => 'uid'),
                'spid' => array('tablename' => 'entities', 'attrselect' => 'companyname', 'attrwhere' => 'eid'),
                'affid' => array('tablename' => 'affiliates', 'attrselect' => 'name', 'attrwhere' => 'affid'),
                'pid' => array('tablename' => 'products', 'attrselect' => 'name', 'attrwhere' => 'pid'),
                'psid' => array('tablename' => 'productsegments', 'attrselect' => 'title', 'attrwhere' => 'psid'),
                'coid' => array('tablename' => 'countries', 'attrselect' => 'name', 'attrwhere' => 'coid')
        );
        $associations_query = $db->query("SELECT * FROM ".Tprefix."surveys_associations WHERE sid={$this->survey[sid]}");
        if($db->num_rows($associations_query) > 0) {
            while($association = $db->fetch_assoc($associations_query)) {
                if(isset($options[$association['attr']])) {
                    $associations[$association['attr']] = $db->fetch_field($db->query("SELECT {$options[$association[attr]][attrselect]} FROM {$options[$association[attr]][tablename]} WHERE {$options[$association[attr]][attrwhere]}={$association[id]}"), $options[$association['attr']]['attrselect']);
                }
                else {
                    $associations[$association['attr']] = $association['id'];
                }
            }
            return $associations;
        }
        return false;
    }

    public function check_invitation($uid = '', $sid = '') {
        global $core, $db;

        if(empty($uid)) {
            $uid = $core->user['uid'];
        }

        if(empty($sid) && isset($this->survey['sid'])) {
            $sid = $this->survey['sid'];
        }
        elseif(empty($sid) && !isset($this->survey['sid'])) {
            return false;
        }

        if(value_exists('surveys_invitations', 'invitee', $uid, 'sid='.$db->escape_string($sid))) {
            return true;
        }
        return false;
    }

    private function randomly_select_invitations(array $limits) {
        if(empty($limits)) {
            return false;
        }
        foreach($limits as $group => $limit) {
            if($limit > count($this->survey['invitations'][$group])) {
                continue;
            }
            if(!empty($limit)) { /* Pick $limit number entries out of the invitations group array */
                $this->survey['invitations'][$group] = array_rand($this->survey['invitations'][$group], $limit);
            }
        }
    }

    /*
     * @param	int		$stqid					Question id
     * @return	array	$question_responses		All responses for each question
     */
    public function get_question_responses($stqid) {
        global $db;

        if(!empty($stqid)) {
            $query = $db->query("SELECT sqc.stqid, sr.identifier, sr.comments, sqc.choice
						FROM ".Tprefix."surveys_responses sr
						JOIN ".Tprefix."surveys_templates_questions_choices sqc ON (sqc.stqcid=sr.response)
						WHERE sr.stqid=".$db->escape_string($stqid).' AND sid='.$this->survey['sid']);
            if($db->num_rows($query) > 0) {
                while($question_response = $db->fetch_assoc($query)) {
                    if(isset($question_responses[$question_response['identifier']])) {
                        $question_responses[$question_response['identifier']]['choices'][$question_response['choice']] = $question_response['choice'];
                    }
                    else {
                        $question_responses[$question_response['identifier']] = $question_response;
                        $question_responses[$question_response['identifier']]['choices'][$question_response['choice']] = $question_response['choice'];
                    }
                }
                return $question_responses;
            }
            else {
                return false;
            }
        }
        return false;
    }

    /*
     * Returns the validation options of each of the survey's questions
     * @return	array	$questions		Array containing the validations
     */
    private function get_questions_validations() {
        global $db;

        $query = $db->query("SELECT stqid, isRequired, validationType, validationCriterion, hasValidation
					FROM ".Tprefix."surveys_templates_sections sts
					JOIN ".Tprefix."surveys_templates_questions stq ON (sts.stsid=stq.stsid)
					JOIN ".Tprefix."surveys_questiontypes sqt ON (sqt.sqtid=stq.type)
					WHERE sts.stid={$this->survey[stid]}");
        while($question = $db->fetch_assoc($query)) {
            $questions[$question['stqid']] = $question;
        }
        return $questions;
    }

    public function get_questions() {
        global $db;

        $query = $db->query("SELECT *, sts.title AS section_title, stq.description AS description
							FROM ".Tprefix."surveys_templates st
							JOIN ".Tprefix."surveys_templates_sections sts ON (sts.stid=st.stid)
							JOIN ".Tprefix."surveys_templates_questions stq ON (sts.stsid=stq.stsid)
							JOIN ".Tprefix."surveys_questiontypes sqt ON (sqt.sqtid=stq.type)
							WHERE st.stid={$this->survey[stid]}
							ORDER BY sequence ASC");

        while($question = $db->fetch_assoc($query)) {
            $choices = array();
            if($question['hasChoices'] == 1) {
                $query2 = $db->query("SELECT * FROM ".Tprefix."surveys_templates_questions_choices WHERE stqid={$question[stqid]} ORDER BY stqcid ASC");
                while($choice = $db->fetch_assoc($query2)) {
                    $question['choices'][$choice['stqcid']] = $choice['choice'];
                }
            }
            $questions[$question['stsid']]['section_title'] = $question['section_title'];
            $questions[$question['stsid']]['questions'][$question['stqid']] = $question;
        }

        return $questions;
    }

    public function parse_question(array $question, $secondary = false, array $response = array(), $isquiz = 0) {
        $question_output_requiredattr = '';
        $rowclass = '';
        if($question['isRequired'] == 1) {
            $question_output_required = '<span class="red_text">*</span>';
            $question_output_requiredattr = ' required="required"';
        }

        if($secondary == true) {
            $question_output = '<div style="margin: 5px 0px 5px 20px; font-style:italic; ">'.$question['question'].'</div>';
        }
        else {
            if(isset($question['description'])) {
                $question_desc_output = '<div class="altrow2" style="margin-left:15px; font-style: italic;font-weight:normal;">'.$question['description'].'</div>';
            }

            $question_output = '<div  class="altrow2" style="padding-bottom:10px; padding-top:10px; font-weight: bold;">'.$question['sequence'].' - '.$question['question'].$question_output_required.$question_desc_output.'</div>';
        }

        switch($question['fieldType']) {
            case 'textbox':
                if($question['fieldSize'] == 0) {
                    $question['fieldSize'] = 50;
                }

                $question_output_inputaccept = '';
                if($question['validationType'] == 'numeric') {
                    $question_output_inputaccept = ' accept="numeric"';
                }

                if(is_array($question['choices']) && !empty($question['choices'])) {
                    if(!empty($response)) {
                        foreach($response as $values) {
                            $question_output .= '<div style="margin-left:20px;">'.$values['choice'].': '.$values['response'].'</div>';
                        }
                    }
                    else {
                        foreach($question['choices'] as $key => $choice) {
                            $question_output .= '<div style="margin-left:20px;">'.$choice.' <input type="text" id="answer_actual_'.$question['stqid'].'_'.$key.'" name="answer[actual]['.$question['stqid'].']['.$key.']" size="'.$question['fieldSize'].'"'.$question_output_inputaccept.$question_output_requiredattr.' /> <input type="hidden" id="answer_comments_'.$question['stqid'].'_'.$key.'" name="answer[comments]['.$question['stqid'].']['.$key.']" value="'.$key.'"/>'.$this->parse_validation($question).'</div>';
                        }
                    }
                }
                else /* Single textbox */ {
                    if(!empty($response)) {
                        $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.$response['response'].'</div>';
                    }
                    else {
                        $question_output_idadd = '[actual]';
                        if($secondary == true) {
                            $question_output_idadd = '[comments]';
                        }

                        $question_output .= '<div style="margin: 5px 20px; 5px; 20px;"><input type="text" id="answer_'.$question_output_idadd.'_'.$question['stqid'].'" name="answer'.$question_output_idadd.'['.$question['stqid'].']" size="'.$question['fieldSize'].'"'.$question_output_inputaccept.$question_output_requiredattr.' /> '.$this->parse_validation($question).'</div>';
                    }
                }
                break;
            case 'selectlist':
                if(!empty($response)) {
                    if($question['hasMultiAnswers'] == 0) {
                        if($isquiz == 1) {
                            if($response['isCorrect'] == 1) {
                                $rowclass = 'greenbackground';
                            }
                            else {
                                $rowclass = 'unapproved';
                            }
                        }
                        $question_output .= '<div class="'.$rowclass.'" style="margin: 5px 20px; 5px; 20px;">'.$response['choice'].'</div>';
                    }
                    else {
                        foreach($response as $attr => $value) {
                            $rowclass = '';
                            if($isquiz == 1) {
                                if($value['isCorrect'] == 1) {
                                    $rowclass = 'greenbackground';
                                }
                                else {
                                    $rowclass = 'unapproved';
                                }
                            }
                            $question_output_response.= '<span class="'.$rowclass.'">'.$value['choice'].'</span> ,';
                        }
                        $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.$question_output_response.'</div>';
                    }
                }
                else {
                    $question_output .= '<div style="margin: 5px 20px; 5px; 20px;"> '.parse_selectlist('answer[actual]['.$question['stqid'].'][]', $question['order'], $question['choices'], '', $question['hasMultiAnswers'], '', array('required' => $question['isRequired'])).'</div>';
                }
                break;
            case 'checkbox':
                if(!empty($response)) {
                    foreach($response as $attr => $value) {
                        $rowclass = '';
                        if($isquiz == 1) {
                            if($value['isCorrect'] == 1) {
                                $rowclass = 'greenbackground';
                            }
                            else {
                                $rowclass = 'unapproved';
                            }
                        }
                        $question_output_response.= '<span class="'.$rowclass.'">'.$value['choice'].'</span> ,';
                    }
                    $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.$question_output_response.'</div>';
                }
                else {
                    $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.parse_checkboxes('answer[actual]['.$question['stqid'].']', $question['choices'], '', true, '&nbsp;&nbsp;').'</div>';
                }
                break;
            case 'radiobutton':
                if(!empty($response)) {
                    if($isquiz == 1) {
                        if($response['isCorrect'] == 1) {
                            $rowclass = 'greenbackground';
                        }
                        else {
                            $rowclass = 'unapproved';
                        }
                    }
                    $question_output .= '<div class="'.$rowclass.'" style="margin: 5px 20px; 5px; 20px;">'.$response['choice'].'</div>';
                }
                else {
                    $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.parse_radiobutton('answer[actual]['.$question['stqid'].']', $question['choices'], '', true, '&nbsp;&nbsp;', array('required' => $question['isRequired'])).'</div>';
                }
                break;
            case 'textarea':
                if(!empty($response)) {
                    $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.$response['response'].'</div>';
                }
                else {
                    $question_output_idadd = '[actual]';
                    if($secondary == true) {
                        $question_output_idadd = '[comments]';
                    }
                    $question_output .= '<div style="margin: 5px 20px; 5px; 20px;"><textarea id="answer_'.$question_output_idadd.'_'.$question['stqid'].'" name="answer'.$question_output_idadd.'['.$question['stqid'].']" cols="50" rows="'.$question['fieldSize'].'"'.$question_output_requiredattr.'></textarea> '.$this->parse_validation($question).'</div>';
                }
                break;
            default: return false;
        }

        if($question['hasCommentsField'] == 1) {
            if(!empty($response)) {
                if(empty($response['comments'])) {
                    $response['comments'] = '-';
                }
                $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.$question['commentsFieldTitle'].': '.$response['comments'].'</div>';
            }
            else {
                $question_output .= $this->parse_question(array('stqid' => $question['stqid'], 'question' => $question['commentsFieldTitle'], 'fieldType' => $question['commentsFieldType'], 'fieldSize' => $question['commentsFieldSize']), true);
            }
        }
        return $question_output;
    }

    /*
     * Checks whether user is a former respondant
     */
    public function check_respondant($sid = '') {
        global $core;

        if(empty($sid)) {
            $sid = $this->survey['sid'];
        }

        if(value_exists('surveys_responses', 'sid', $sid, 'invitee='.$core->user['uid'])) {
            return true;
        }
    }

    public function get_status() {
        return $this->status;
    }

    public function get_id() {
        return $this->survey['sid'];
    }

    public function get_erros($type) {
        return $this->survey['errors'][$type];
    }

    public function get_shared_users() {
        global $db;

        $query = $db->query('SELECT uid FROM '.Tprefix.'surveys_sharedwith WHERE sid='.intval($this->survey['sid'].''));
        if($db->num_rows($query)) {
            while($user = $db->fetch_assoc($query)) {
                $uids[] = $user['uid'];
            }
            $users = Users::get_data(array('uid' => $uids), array('operators' => array('uid' => 'IN'), 'simple' => false, 'returnarray' => true));

            return $users;
        }
        return false;
    }

    public function share($survey_data = array()) {
        global $db, $core;
        if(is_array($survey_data)) {
            foreach($survey_data as $key => $val) {
                if(empty($val)) {
                    continue;
                }
                /* get exist users for the current survey */
                $existing_users = $this->get_shared_users();
                /* get the difference between the exist users and the slected users */
                if(is_array($existing_users)) {
                    $existing_users = array_keys($existing_users);
                    $users_toremove = array_diff($existing_users, $survey_data);
                    if(!empty($users_toremove)) {
                        $db->delete_query('surveys_sharedwith', 'uid IN ('.$db->escape_string(implode(',', $users_toremove)).') AND sid='.$this->survey['sid']);
                    }
                }
                $survey_shares['sid'] = $this->survey['sid'];
                $survey_shares['createdBy'] = $core->user['uid'];
                $survey_shares['createdOn'] = TIME_NOW;
                $survey_shares['uid'] = $core->sanitize_inputs($val);
                if(!value_exists('surveys_sharedwith', 'uid', $val, ' sid='.$this->survey['sid'])) {
                    $query = $db->insert_query('surveys_sharedwith', $survey_shares);
                    if(!$query) {
                        return false;
                    }
                    $this->notify_shareduser($survey_shares['uid']);
                }
            }
        }
        return true;
    }

    private function notify_shareduser($uid) {
        global $core, $lang;

        $user_obj = new Users($uid);
        if(!is_object($user_obj)) {
            return false;
        }
        $share_user = $user_obj->get();

        $lang->load('messages');
        $surveylink = '<a href="'.DOMAIN.'/index.php?module=surveys/viewresults&amp;referrer=sharedlist&amp;identifier='.$this->survey['identifier'].'">'.$this->survey['subject'].'</a>';
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_subject($this->survey['subject'].' Results');
        $message = $this->survey['subject'].' Results has been shared with you: Click here to view the results '.$surveylink;
        $mailer->set_message($message);
        $mailer->set_from(array('name' => $core->user['displayName'], 'email' => $core->user['email']));
        $mailer->set_to($share_user['email']);
        $mailer->send();
    }

    public function __get($name) {
        if(isset($this->survey[$name])) {
            return $this->survey[$name];
        }
        return false;
    }

    public function __set($name, $value) {
        $this->survey[$name] = $value;
    }

}
?>