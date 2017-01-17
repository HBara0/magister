<div class="row">
    <div class="col-md-9 col-lg-9 col-sm-12">
        <h1>{$recommendation_displayname}</h1>
    </div>
    <div class="col-md-3 col-lg-3 col-sm-12" >
        <div class="btn-group">
            <button type="button" class="btn btn-success" onclick="window.open('{$editlink}', '_blank')">{$lang->managerecommendation}
            </button>
            <button {$hide_managerecommendationbutton} type="button" class="btn btn-primary" onclick="window.open('{$editlink}', '_blank')">{$lang->managerecommendation}
            </button>
        </div>
    </div>
</div>
</div>
<div class="panel panel-success" {$hide_recommendatinodescription}>
    <div class="panel-heading">{$lang->description}</div>
    <div class="panel-body">
        {$recommendation[description]}
    </div>
</div>