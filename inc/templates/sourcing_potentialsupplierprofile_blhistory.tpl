<div class="thead"style="margin-top: 10px;">{$lang->blhistory}</div>
<div style="overflow:auto; width:100%; max-height:200px; vertical-align:top;">
    <table class="datatable datatable-striped" width="100%">
        <thead>
            <tr>
                <th style="width: 15%;">{$lang->datebacklisted}</th>
                <th style="width: 15%;">{$lang->dateunblacklisted}</th>
                <th style="width: 15%;">{$lang->requestedby}</th>
                <th>{$lang->reason}</th>
            </tr>
        </thead>
        {$blacklist_historiesrows}
    </table>
</div>