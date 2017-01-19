<div class="modal-dialog" >
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
                    <h1>{$addbutton}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-lg-12 col-xs-12 ">
                    <h4>{$daterangeoutput}</h4>
                </div>
            </div>
            <div class="row">
                <div class=" panel panel-success">
                    <div class="panel-heading">{$lang->description}</div>
                    <div class="panel-body">
                        {$event[description]}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
