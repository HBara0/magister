<?php

if (!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if ($core->usergroup['canAccessSystem'] == 0) {
    error($lang->sectionnopermission);
}
if (!isset($core->input['action'])) {
    $timetablelink = "https://docs.google.com/spreadsheets/d/1wjDsYACh2xeBvbarftwx-ne_izs1uPFvhT8Ko4qn_Rg/edit#gid=1364854183";
    eval("\$timetable= \"" . $template->get('timetable') . "\";");
    output_page($timetable);
}