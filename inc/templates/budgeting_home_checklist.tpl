<div style="width:50%;margin: auto">
    <ul class="list-group">
        <li class="list-group-item">
            <button type="button" data-toggle="collapse" {$status['rates']['disable']} data-target="#rates" aria-expanded="false" aria-controls="rates" style="background-color: {$status['rates']['color']};height: 50px;width: 100%" class="button">
                {$lang->budgetconversionrates}<span style='height:20px;font-size: 15px;color:{$status['rates']['color']}; background-color: #f8ffcc;text-align: center' class="label label-pill pull-right">{$status['rates']['count']}</span>
            </button>
            <div class="collapse" id="rates" >
                <div class="well">
                    {$corrections['rates']}
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <button type="button" data-toggle="collapse" {$status['yef']['disable']} data-target="#yef" aria-expanded="false" aria-controls="yef" style="background-color: {$status['yef']['color']};height: 50px;width: 100%" class="button">
                {$lang->yearendforecast}<span style='height:20px;font-size: 15px;color:{$status['yef']['color']}; background-color: #f8ffcc;text-align: center' class="label label-pill pull-right">{$status['yef']['count']}</span>
            </button>
            <div class="collapse" id="yef" >
                <div class="well">
                    {$corrections['yef']}
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <button type="button" {$status['budget']['disable']} data-toggle="collapse" data-target="#budget" aria-expanded="false" aria-controls="budget" style="background-color: {$status['budget']['color']};height: 50px;width: 100%" class="button">
                {$lang->commercialbudget}<span style='height:20px;font-size: 15px;color:{$status['budget']['color']}; background-color: #f8ffcc;text-align: center' class="label label-pill pull-right">{$status['budget']['count']}</span>
            </button>
            <div class="collapse" id="budget" >
                <div class="well">
                    {$corrections['budget']}
                </div>
            </div>
        </li>
        <li {$hidefinance} class="list-group-item">
            <button type="button" data-toggle="collapse" {$status['fin']['disable']} data-target="#fin" aria-expanded="false" aria-controls="fin" style="background-color: {$status['fin']['color']};height: 50px;width: 100%;" class="button">
                {$lang->financialbudget}<span style='height:20px;font-size: 15px;color:{$status['fin']['color']}; background-color: #f8ffcc;text-align: center' class="label label-pill pull-right">{$status['fin']['count']}</span>
            </button>
            <div class="collapse" id="fin" >
                <div class="well">
                    {$corrections['fin']}
                </div>
            </div>
        </li>
    </ul>
</div>