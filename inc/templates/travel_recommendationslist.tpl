<script>
    $(function() {
        $(document).on("click", "a[id^='openmodal_']", function() {
            var href = $(this).data('url');
            $.ajax({
                type: 'post',
                url: href,
                beforeSend: function() {
                    loadgif($("#recommendation_modal").find('.modal-body'));
                    $("#recommendation_modal").modal('show');
                },
                success: function(returnedData) {
                    $("#recommendation_modal").find('.modal-body').html(returnedData);
                }
            })
        });
    });
</script>
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
<div class="modal fade" id="recommendation_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header ">
                <h4 class="modal-title" >{$lang->viewrecommendation}</h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>