<div class="row">
    <div class="col-md-9 col-lg-9 col-sm-12">
        <h1>{$lang->travelrecommendations}</h1>
    </div>
    <div class="col-md-3 col-lg-3 col-sm-12">
        <button type="button" class="btn btn-success" onclick="window.open('{$core->settings['rootdir']}/index.php?module=travel/managerecommendation', '_blank')">{$lang->createrecommendation}
        </button>
    </div>
</div>
<table class="datatable_basic table table-bordered row-border hover order-column" data-checkonclick=true cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th>{$lang->title}</th>
            <th>{$lang->city}</th>
            <th>{$lang->category}</th>
            <th>{$lang->description}</th>
            <th>{$lang->rating}</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>&nbsp;</th>
            <th>{$lang->title}</th>
            <th>{$lang->city}</th>
            <th>{$lang->category}</th>
            <th>{$lang->description}</th>
            <th>{$lang->rating}</th>
        </tr>
    </tfoot>
    <tbody>
        {$recommendations_rows}
    </tbody>
</table>