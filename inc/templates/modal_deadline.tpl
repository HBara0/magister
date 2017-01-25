<div class="modal-dialog modal-lg" >
    <div class="modal-content">
        <div class="modal-header ">
            <h4 class="modal-title" >{$lang->deadlineprofile}</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <h3>{$course_output}</h3>
            </div>
            <div class="row">
                <div class="col-md-9 col-lg-9 col-xs-12 ">
                    <h1>{$deadline[title]}</h1>
                </div>
                <div class="col-md-3 col-lg-3 col-xs-12 ">
                    <h1>{$manageevent_button}</h1>
                    <h1>{$addorremovecourse_button}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-6 col-xs-12 ">
                    <h5> {$lang->date}: {$deadline['time_output']}</h5>
                </div>
            </div>
            <div class="row" {$display_description}>
                <div class=" panel panel-success">
                    <div class="panel-heading">{$lang->description}</div>
                    <div class="panel-body">
                        {$deadline[description]}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
