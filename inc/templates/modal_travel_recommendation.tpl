<div class="modal-dialog modal-lg" >
    <div class="modal-content">
        <div class="modal-header ">
            <h4 class="modal-title" >{$lang->viewrecommendation}</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-9 col-lg-9 col-xs-12 ">
                    <h1>{$recommendation[displayname]}</h1>
                </div>
                <div class="col-md-3 col-lg-3 col-xs-12 ">
                    <h1>{$addbutton}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9 col-xs-12 col-lg-9">
                    <h3>{$recommendation[additionaloutput]}</h3>
                </div>
                <div class="col-md-3 col-xs-12 col-lg-3">
                    {$recommendation[ratingoutput]}
                </div>
            </div>
            <div class="row">
                <div class=" panel panel-success">
                    <div class="panel-heading">{$lang->description}</div>
                    <div class="panel-body">
                        {$recommendation[description]}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
