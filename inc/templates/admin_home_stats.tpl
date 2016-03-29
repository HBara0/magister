<h1>{$lang->welcomeap}</h1>
{$newsuggestions}
<strong>{$lang->systemoverview}</strong>
<ul>
    <li>{$lang->usersstats}</li>
    <li>{$lang->entitiesstats}
        <ul>
            <li>{$lang->suppliersstats}</li>
            <li>{$lang->customersstats}</li>
        </ul>
    </li>
    <li>{$lang->productsstats}</li>

    <li>{$stats[noqreportreq]} {$lang->qreportsrequired}</li>
    <li>{$stats[noqreportsend]} {$lang->qreportstosend}</li>
    <li>{$stats[count_qreport]} {$lang->qreport}</li>
    <li>{$stats[count_visitreport]} {$lang->visitreport}</li>
    <li>{$stats[sharedfiles]} {$lang->sharedfiles}</li>
</ul>
<br />
<strong>{$lang->leaves} </strong><ul>{$stats[leaves]}</ul>
<br />
<strong>{$lang->ocosusers} </strong><ul>{$stats[ocosusers]}</ul>
<br />
<hr />
<p class="subtitle">{$lang->numusersonline}</p>
{$onlineusers}
