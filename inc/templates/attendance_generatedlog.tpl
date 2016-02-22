<div class="container">
    <h1>{$lang->attendancelog}
        <small><br />{$lang->fromdate} {$report[fromdate_output]} {$lang->todate} {$report[todate_output]}</small>
    </h1>
    <span> < : {$lang->arrivearly} | > : {$lang->leavelater} | <> : {$lang->earlyandlate} | H: {$lang->holiday} | W/E : {$lang->weekend} | L : {$lang->leave} | UL : {$lang->unpaidleave}</span>
    </hr>
    <div>
        {$output}
    </div>
    <div align="right">{$tools}</div>
</div>