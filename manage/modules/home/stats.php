<?php

if (!$core->input['action']) {
    $query = $db->query("SELECT eid, companyName FROM " . Tprefix . "entities WHERE approved='0' AND type='s' AND dateAdded>'{$core->user[lastVisit]}' LIMIT 0,3");
    if ($db->num_rows($query) > 0) {
        while ($supplier = $db->fetch_array($query)) {
            $suggestions_list .= "<li>{$supplier[companyName]} - <a href='#' id='approve_entities/edit_approved_1_{$supplier[eid]}' class='green_text'>{$lang->approve}</a></li>";
        }

        $lang->followingbeensuggested = $lang->sprint($lang->followingbeensuggested, strtolower($lang->suppliers));
        eval("\$newsuggestions = \"" . $template->get("admin_suggestnotification_box") . "\";");
    }

    $lang->usersstats = $lang->sprint($lang->usersstats, $db->fetch_field($db->query("SELECT COUNT(*) as countusers FROM " . Tprefix . "users where gid!=7"), "countusers"));

    $entities = $db->query("SELECT COUNT(type) AS count, type FROM " . Tprefix . "entities GROUP BY type");
    while ($entity_count = $db->fetch_array($entities)) {
        if ($entity_count['type'] == "c") {
            $customers_count = $entity_count['count'];
        }
        elseif ($entity_count['type'] == "s") {
            $suppliers_count = $entity_count['count'];
        }
        $entities_count += $entity_count['count'];
    }

    $lang->entitiesstats = $lang->sprint($lang->entitiesstats, $entities_count);
    $lang->customersstats = $lang->sprint($lang->customersstats, $customers_count);
    $lang->suppliersstats = $lang->sprint($lang->suppliersstats, $suppliers_count);

    $count_products = $db->fetch_field($db->query("SELECT COUNT(*) as countproducts FROM " . Tprefix . "products"), "countproducts");
    $count_generics = $db->fetch_field($db->query("SELECT COUNT(*) as countgenerics FROM " . Tprefix . "genericproducts"), "countgenerics");
    $count_segments = $db->fetch_field($db->query("SELECT COUNT(*) as countsegments FROM " . Tprefix . "productsegments"), "countsegments");

    $lang->productsstats = $lang->sprint($lang->productsstats, $count_products, $count_generics, $count_segments);


    $online = $db->query("SELECT DISTINCT(u.username), u.uid FROM " . Tprefix . "sessions s LEFT JOIN " . Tprefix . "users u ON (u.uid=s.uid) WHERE s.uid!=0 ORDER BY s.time DESC");
    $count_online = 0;
    while ($onlineuser = $db->fetch_array($online)) {
        $onlineusers .= "{$onlineusers_comma}<a href='../users.php?action=profile&amp;uid={$onlineuser[uid]}'>{$onlineuser[username]}";
        $onlineusers_comma = ", ";
        $count_online++;
    }
    $lang->numusersonline = $lang->sprint($lang->numusersonline, $count_online);


    $stats['noqreportreq'] = sizeof(get_specificdata('entities', 'eid, companyName', 'eid', 'companyName', '', '', 'noQReportReq = 1'));
    $stats['noqreportsend'] = sizeof(get_specificdata('entities', 'eid, companyName', 'eid', 'companyName', '', '', 'noQReportSend = 1'));
    $stats['count_qreport'] = $db->fetch_field($db->query("SELECT count(*) as countall FROM " . Tprefix . "reports WHERE type='q' AND status='1'"), 'countall');
    $stats['count_visitreport'] = $db->fetch_field($db->query("SELECT count(*) as countall FROM " . Tprefix . "visitreports"), 'countall');
    $stats['sharedfiles'] = $db->fetch_field($db->query("SELECT count(*) as countall FROM " . Tprefix . "files WHERE isShared='1'"), 'countall');
    $affname_query = $db->query("SELECT affid,name FROM " . Tprefix . "affiliates");
    while ($affname = $db->fetch_assoc($affname_query)) {
        $affiliate_name[$affname['affid']] = $affname['name'];
    }

    $leaves_query = $db->query("SELECT COUNT(*) as countall, ae.affid  FROM " . Tprefix . "leaves l JOIN " . Tprefix . "affiliatedemployees ae ON (l.uid=ae.uid) WHERE ae.isMain=1 GROUP BY ae.affid");

    while ($leaves = $db->fetch_assoc($leaves_query)) {
        $stats['leaves'] .= '<li>' . $leaves['countall'] . ' by employees in ' . $affiliate_name[$leaves['affid']] . '</li>';
    }

    $magisterusers = $db->query("SELECT COUNT(*) as countall, affid FROM " . Tprefix . "affiliatedemployees ae JOIN " . Tprefix . "users u ON (u.uid=ae.uid) WHERE isMain=1 AND gid!=7 GROUP BY affid");
    while ($users = $db->fetch_assoc($magisterusers)) {
        $stats['magisterusers'] .= '<li>' . $users['countall'] . ' employees in ' . $affiliate_name[$users['affid']] . '</li>';
    }


    eval("\$stats = \"" . $template->get('admin_home_stats') . "\";");
    output_page($stats);
}
?>