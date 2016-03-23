<style type="text/css">
    .currentkpi, .lastkpi {
        padding: 0px !important;
        width: 49%;
    }

    .currentkpi {
        font-size:18px;
    }

    .currentkpi_value {
        font-size: 40px;
    }

    .lastkpi {
        font-size:15px;
        font-weight:lighter;
    }

    .lastkpi_value {
        font-size:25px;
    }

    .kpititle {
        font-size:20px;
    }

</style>

<h1>{$lang->workspace}</h1>
<div style="width:50%; height:50%;" class="border_bottom border_right">
    <div class="kpititle">{$kpi_config[name]}</div>
    <div style="display: block; margin-left:23px; padding: 0px;">
        <div style="display:inline-block;" class="currentkpi">Current Period</div>
        <div style="display:inline-block; text-align: right;" class="currentkpi_value currentkpi {$kpi_class[current]}">{$kpis[1][current]}% {$trend_output}</div>
    </div>
    <div style="display: block; margin-left:23px; padding: 0px;">
        <div style="display:inline-block;" class="lastkpi">Previous Month</div>
        <div style="display:inline-block; text-align: right; margin-right: 15px; width: 40%;" class="lastkpi_value lastkpi {$kpi_class[lastmonth]}">{$kpis[1][lastmonth]}%</div>
    </div>
</div>
