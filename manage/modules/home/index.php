<?php

if (!$core->input['action']) {
    $lang->usersstats = $lang->sprint($lang->usersstats, $db->fetch_field($db->query("SELECT COUNT(*) as countusers FROM " . Tprefix . "users"), "countusers"));



    $searchtime = TIME_NOW - 900;
    $online = $db->query("SELECT DISTINCT(u.username), u.uid FROM " . Tprefix . "sessions s LEFT JOIN " . Tprefix . "users u ON (u.uid=s.uid) WHERE s.uid!=0 AND s.time > {$searchtime} ORDER BY s.time DESC");
    $count_online = 0;
    while ($onlineuser = $db->fetch_array($online)) {
        $onlineusers .= "{$onlineusers_comma}<a href='../users.php?action=profile&amp;uid={$onlineuser[uid]}'>{$onlineuser[username]}";
        $onlineusers_comma = ", ";
        $count_online++;
    }
    $lang->numusersonline = $lang->sprint($lang->numusersonline, $count_online);

    eval("\$index = \"" . $template->get("admin_index") . "\";");
    output_page($index);
}
?>