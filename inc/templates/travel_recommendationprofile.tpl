<div class="row">
    <div class="col-md-9 col-lg-9 col-sm-12">
        <h1>{$recommendation[displayname]}</h1>
    </div>
    <div class="col-md-3 col-lg-3 col-sm-12" >
        <div class="btn-group">
            <button {$hide_managerecommendationbutton} type="button" class="btn btn-primary" onclick="window.open('{$editlink}', '_blank')">{$lang->managerecommendation}
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9 col-xs-12 col-lg-9">
            <h3>{$recommendation[additionaloutput]}  &nbsp;&nbsp;&nbsp;<small>{$recommendation[ratingoutput]}</small></h3>
        </div>
    </div>
</div>
<hr><br><br>
<div class="panel panel-success" {$hide_recommendatinodescription}>
    <div class="panel-heading">{$lang->description}</div>
    <div class="panel-body">
        {$recommendation[description]}
    </div>
</div>
