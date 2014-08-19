<div class="thead"style="margin-top: 10px;"> {$lang->blhistory}</div>


<div style="display:inline-block; overflow:auto; width:100%; max-height:200px; vertical-align:top; margin-top:10px;">
    <table class="datatable datatable-striped" width="100%">
        <thead>
            <tr>
                <th>{$lang->blhistorytime}</th>
                <th>{$lang->blhistoryremoval}</th>
                <th>{$lang->requestedby}</th>
                <th>{$lang->reason}</th>
            </tr>
        </thead>
        {$blacklist_historiesrows}
    </table>
    <hr />
</div>