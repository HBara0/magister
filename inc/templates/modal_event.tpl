<script>
    $(function() {
        $(document).on("click", "button[id^='subscribebutton']", function() {
            var id = $(this).attr("id").split("_");
            $.ajax({
                type: 'post',
                url: "{$core->settings['rootdir']}/index.php?module=events/eventslist&id=" + id[1] + "&action=events_" + id[2],
                data: "id=" + id[1],
                beforeSend: function() {
                    loadgif($("#subscribedive_" + id[1]));

                },
                success: function(returnedData) {
                    $("#subscribedive_" + id[1]).html(returnedData);
                }
            })
        });

    });
</script>
<div class="modal-dialog modal-lg" >
    <div class="modal-content">
        <div class="modal-header ">
            <h4 class="modal-title" >{$lang->eventprofile}</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-9 col-lg-9 col-xs-12 ">
                    <h1>{$event[title]}</h1>
                </div>
                <div class="col-md-3 col-lg-3 col-xs-12 ">
                    <h1>{$addorremovecourse_button}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-6 col-xs-12 ">
                    <h5> {$lang->from}: {$event['fromdate_output']}</h5>
                </div>
                <div class="col-md-6 col-lg-6 col-xs-12 ">
                    <h5>   {$lang->to}:  {$event['todate_output']}</h5>
                </div>
            </div>
            <div class="row" >
                <div class=" panel panel-success">
                    <div class="panel-heading">{$lang->description}</div>
                    <div class="panel-body">
                        {$event[description]}
                    </div>
                </div>
            </div>
            <div class="row" {$hideattendees}>
                <div class=" panel panel-success">
                    <div class="panel-heading">{$lang->attendees}</div>
                    <div class="panel-body">
                        {$event[attendees_output]}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
