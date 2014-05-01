<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * CMS News Class
 * $id: HrVancancies_class.php
 * Created:			@tony.assaad	September 18, 2012 | 11:00 PM
 * Last Update: 	@tony.assaad	September 28, 2012 | 11:55  PM
 */

class HrVancancies {
    protected $status = 0;
    private $vacancy = array();

    public function __construct($id = '', $simple = false) {
        if(isset($id) && !empty($id)) {
            $this->vacancy = $this->read($id, $simple);   /* Read the vacancy from the function and store the returned data in var vacancy */
        }
    }

    public function create_vacancy($data, array $options = array()) {
        global $db, $log, $core, $errorhandler, $lang;

        if(isset($data['approxJoinDate']) && !empty($data['approxJoinDate'])) {
            $data['approxJoinDate'] = strtotime($data['approxJoinDate']);
        }
        if(isset($data['publishOn']) && !empty($data['publishOn'])) {
            $data['publishOn'] = strtotime($data['publishOn']);
        }
        else {
            $data['publishOn'] = 0;
        }
        if(isset($data['unpublishOn']) && !empty($data['unpublishOn'])) {
            $data['unpublishOn'] = strtotime($data['unpublishOn']);
        }
        else {
            $data['unpublishOn'] = 0;
        }
        if(isset($data['publishingTimeZone']) && !empty($data['publishingTimeZone'])) {
            $data['publishingTimeZone'] = strtotime($data['publishingTimeZone']);
        }

        unset($data['filterIndustry'], $data['interviewquestions']);  // unset  temporary 
        $data['identifier'] = substr(md5(uniqid(microtime())), 1, 10);
        $requiredlang = $data['requiredlang'];
        $this->interview = $data['onlineinterview'];
        unset($data['requiredlang'], $data['onlineinterview']);
        $this->vacancy = $data;
        $this->vacancy['dateCreated'] = TIME_NOW;
        $this->vacancy['createdBy'] = $core->user['uid'];
        print_r($this->interview);
        /* ---SANITIZE INPUTS---START */
        $sanitize_fields = array('reference', 'title', 'shortDesc', 'responsibilities', 'minQualifications', 'prefQualifications');
        foreach($sanitize_fields as $val) {
            $this->vacancy[$val] = $core->sanitize_inputs($this->vacancy[$val], array('removetags' => true));
        }
        /* ---SANITIZE INPUTS---END */

        $required_fields = array('employmentType', 'title', 'workLocation', 'responsibilities', 'shortDesc');
        foreach($required_fields as $val) {
            if(empty($this->vacancy[$val])) {
                $this->status = 1;
                return false;
            }
        }
        /* Verify if user can HR this affiliate Server side --START */
        if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
            if(!in_array($this->vacancy['affid'], $core->user['hraffids'])) {
                return false;
            }
        }
        /* Verify if user can HR this affiliate Server side --END */

        if(value_exists('hr_vacancies', 'affid', $this->vacancy['affid'], '(('.TIME_NOW.' BETWEEN '.$this->vacancy['publishOn'].' AND '.$this->vacancy['unpublishOn'].') OR title="'.$this->vacancy['title'].'" )')) {
            $this->status = 4;
            return false;
        }

        $this->vacancy['title'] = $core->sanitize_inputs($this->vacancy['title'], array('removetags' => true));

        if(is_array($this->vacancy)) {
            $query = $db->insert_query('hr_vacancies', $this->vacancy);
        }
        /* Insert vacancies reqlangs -START */
        if($query) {
            $hrvid = $db->last_id(); /* Get the ID generated in the last insert query  */
            $log->record('hr_managejobopportunity');
            if(is_array($requiredlang)) {
                foreach($requiredlang as $reqlang) {
                    $reqlang_data = array(
                            'hrvid' => $hrvid,
                            'lang' => $reqlang
                    );
                    $query = $db->insert_query('hr_vacancies_reqlangs', $reqlang_data);
                }
            }
        }/* Insert vacancies reqlangs -END */
        foreach($this->interview as $key => $questions) {
            print_r($questions);
            $interviewquestions = array(
                    'hrvid' => $hrvid,
                    'question' => $questions['question'],
                    'readingTime' => $questions['readingTime'],
                    'answeringTime' => $questions['answeringTime'],
            );
            print_r($interviewquestions);
            //$interview_query = $db->insert_query('hr_vacancies_interviewquestions', $interviewquestions);
        }
    }

    public function get_jobs() {
        global $db, $core;

        $sort_query = 'ORDER BY hv.dateCreated ASC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = 'ORDER BY '.$core->input['sortby'].' '.$core->input['order'];
        }

        if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
            $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
        }

        $limit_start = 0;
        if(isset($core->input['start'])) {
            $limit_start = $db->escape_string($core->input['start']);
        }

        if(isset($core->input['filterby'], $core->input['filtervalue'])) {
            $attributes_filter_options['title'] = array('title' => 'hv.');

            if($attributes_filter_options['title'][$core->input['filterby']] == 'int') {
                $filter_value = ' = "'.$db->escape_string($core->input['filtervalue']).'"';
            }
            else {
                $filter_value = ' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
            }

            $filter_where = ' WHERE '.$db->escape_string($attributes_filter_options['title'][$core->input['filterby']].$core->input['filterby']).$filter_value;
        }

        /* if the user is not HR all affiliates  then can only see the affiliates/countries he/she HR */
        if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
            if(!empty($filter_where)) {
                $wherecondition = 'AND';
            }
            else {
                $wherecondition = 'WHERE';
            }
            $canhr_where = $wherecondition." (affe.canHr=1) AND (affe.uid=".$core->user['uid'].")";
        }
        $query = $db->query("SELECT DISTINCT(hv.hrvid), hv.*
								FROM  ".Tprefix."hr_vacancies hv
								JOIN ".Tprefix."affiliatedemployees affe ON (hv.affid=affe.affid)
								{$filter_where}
								{$canhr_where}
								AND hv.isCanceled=0
								{$sort_query} 
								LIMIT {$limit_start},{$core->settings[itemsperlist]}");
        if($db->num_rows($query) > 0) {
            while($vacancies = $db->fetch_assoc($query)) {
                $job[$vacancies['hrvid']] = $vacancies;
                $job[$vacancies['hrvid']]['countapplicants'] = $db->fetch_field($db->query("SELECT count(hrvaid) as count FROM ".Tprefix."hr_vacancies_applicants WHERE hrvid='{$vacancies[hrvid]}'"), 'count');
            }
            return $job;
        }
        return false;
    }

    public function get_jobapplicants($vacany_id) {
        global $db, $core;

        if(isset($vacany_id) && !empty($vacany_id)) {
            $vacany_id = $db->escape_string($vacany_id);
        }

        $sort_query = 'ORDER BY hv.dateCreated ASC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = 'ORDER BY '.$core->input['sortby'].' '.$core->input['order'];
        }

        if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
            $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
        }

        $limit_start = 0;
        if(isset($core->input['start'])) {
            $limit_start = $db->escape_string($core->input['start']);
        }

        if(isset($core->input['filterby'], $core->input['filtervalue'])) {
            $attributes_filter_options['title'] = array('title' => 'hv.');

            if($attributes_filter_options['title'][$core->input['filterby']] == 'int') {
                $filter_value = ' = "'.$db->escape_string($core->input['filtervalue']).'"';
            }
            else {
                $filter_value = ' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
            }

            $filter_where = ' WHERE '.$db->escape_string($attributes_filter_options['title'][$core->input['filterby']].$core->input['filterby']).$filter_value;
        }

        /* if the user is not HR all affiliates  then can only see the affiliates/countries he/she HR */
        if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
            if(!empty($filter_where)) {
                $wherecondition = 'AND';
            }
            else {
                $wherecondition = 'WHERE';
            }
            $canhr_where = $wherecondition." (affe.canHr=1) AND (affe.uid=".$core->user['uid'].")";
        }
        $query = $db->query("SELECT DISTINCT(hrvapp.hrvaid),hv.hrvid, hrvapp.hrvaid AS applicantid, hrvapp.identifier ,hrvapp.isFlagged, hrvapp.dateSubmitted, CONCAT(hrvapp.firstName,' ', hrvapp.lastName) AS name
								FROM  ".Tprefix."hr_vacancies hv
								JOIN ".Tprefix."hr_vacancies_applicants hrvapp ON (hrvapp.hrvid=hv.hrvid)
								JOIN ".Tprefix."affiliatedemployees affe ON (hv.affid=affe.affid)
								{$filter_where}
								{$canhr_where}
								AND hrvapp.hrvid = {$vacany_id}
								{$sort_query} 
								LIMIT {$limit_start}, {$core->settings[itemsperlist]}");



        if($db->num_rows($query) > 0) {
            while($applicant = $db->fetch_assoc($query)) {
                $jobapplicant[$applicant['hrvaid']] = $applicant;
            }
            return $jobapplicant;
        }
        return false;
    }

    public function moderate($action, $vacancy_id, $inapplicant) {
        global $db, $log, $core;


        foreach($core->input['listCheckbox'] as $applicant_id) {
            $query = $db->query("SELECT hrvapp.hrvid,hv.affid FROM ".Tprefix."hr_vacancies_applicants hrvapp
								JOIN hr_vacancies hv ON (hrvapp.hrvid=hv.hrvid)
								WHERE hrvapp.hrvaid='{$applicant_id}'");

            while($affiliates_applicants = $db->fetch_assoc($query)) {
                if(in_array($affiliates_applicants['affid'], $core->user['hraffids'])) {
                    if($action == 'flag') {
                        $db->update_query('hr_vacancies_applicants', array('isFlagged' => 1), 'hrvid='.$vacancy_id.' AND hrvaid IN('.$inapplicant.')');
                    }
                    elseif($action == 'unflag') {
                        $db->update_query('hr_vacancies_applicants', array('isFlagged' => 0), 'hrvid='.$vacancy_id.' AND hrvaid IN('.$inapplicant.')');
                    }
                    elseif($action == 'delete') {
                        $deleted = $db->delete_query('hr_vacancies_applicants', 'hrvid='.$vacancy_id.' AND hrvaid IN('.$inapplicant.')');
                    }
                    $log->record('hr_listjobsapplicants');
                    return $deleted;
                }
                else {
                    unset($core->input['listCheckbox']);
                    return false;
                }
            }
        }
    }

    public function get_status() {
        return $this->status;
    }

}
?>