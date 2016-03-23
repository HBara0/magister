<div id="widgetinstance_{$instance_checksum}_main" class="panel panel-success">
    <div class="panel-heading"><strong>{$header}</strong>
        <div style="float: right">
            <a href="#none" style="cursor: pointer;" data-additionaldata="dashid={$dashid}" id="editinstance_{$instance_checksum}_portal/dashboard_loadpopupbyid"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp
            <a href="#none" style="cursor: pointer;display:none" id="deleteinstance_{$instance_checksum}_portal/dashboard_loadpopupbyid"><span class="glyphicon glyphicon-remove"></span></a>&nbsp
            <div style='float:right; cursor: move;' class="ui-state-default">
                <span class="widgets-sort-icon ui-icon ui-icon-arrowthick-2-n-s"></span>
            </div>
        </div>
    </div>
    <div class="panel-body">
        {$body}
    </div>
</div>