<div style="width:50%;margin: auto">
    <div class="list-group">
        <button type="button" data-toggle="collapse" {$status['rates']['disable']} data-target="#rates" aria-expanded="false" aria-controls="rates" style="height: 50px;width: 100%" class="list-group-item {$status['rates']['color']}">
            {$lang->budgetconversionrates}<span class="label label-pill pull-right badge">{$status['rates']['count']}</span>
        </button>
        <div class="collapse" id="rates" >
            <div class="well">
                {$corrections['rates']}
            </div>
        </div>
        <button type="button" data-toggle="collapse" {$status['yef']['disable']} data-target="#yef" aria-expanded="false" aria-controls="yef" style="height: 50px;width: 100%" class="list-group-item {$status['yef']['color']}">
            {$lang->yearendforecast}<span class="label label-pill pull-right badge">{$status['yef']['count']}</span>
        </button>
        <div class="collapse" id="yef" >
            <div class="well">
                {$corrections['yef']}
            </div>
        </div>
        <button type="button" {$status['budget']['disable']} data-toggle="collapse" data-target="#budget" aria-expanded="false" aria-controls="budget" style="height: 50px;width: 100%" class="list-group-item {$status['budget']['color']}">
            {$lang->commercialbudget}<span  class="label label-pill pull-right badge">{$status['budget']['count']}</span>
        </button>
        <div class="collapse" id="budget" >
            <div class="well">
                {$corrections['budget']}
            </div>
        </div>
        <button type="button" data-toggle="collapse" {$status['fin']['disable']} data-target="#fin" aria-expanded="false" aria-controls="fin" style="height: 50px;width: 100%; {$hidefinance}" class="list-group-item {$status['fin']['color']}">
            {$lang->financialbudget}<span  class="label label-pill pull-right badge">{$status['fin']['count']}</span>
        </button>
        <div class="collapse" id="fin" >
            <div class="well">
                {$corrections['fin']}
            </div>
        </div>
    </div>
</div>