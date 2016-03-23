<script>
    $(function() {
        $("#quarter").change(function() {
            if(sharedFunctions.checkSession() == false) {
                return;
            }

            var quarter = $(this).val();
            $("#year").empty();
            $("#changequarter_Button").attr("disabled", "true");

            if(quarter != 0) {
                var url = "index.php?module=reporting/stats&action=get_years";
                sharedFunctions.requestAjax("post", url, "quarter=" + quarter, "year_Loading", "year", 1);
            }
        });
        $("#year").change(function() {
            $("#changequarter_Button").removeAttr('disabled');
            if($(this).val() == 0) {
                $("#changequarter_Button").attr("disabled", "true");
            }
        });

    });
</script>
<h1>{$lang->reportsstats} - Q{$core->input[quarter]} {$core->input[year]}</h1>

<div style="font-weight:bold;">{$lang->finalizedstats}</div>
<table class="datatable">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th colspan="2" style="width:18%; text-align:center;">{$lang->beforetime}</th>
            <th colspan="2" style="width:18%; text-align:center;">{$lang->ontime}</th>
            <th colspan="2" style="width:18%; text-align:center;">{$lang->late}</th>
            <th style="width:18%; text-align:center;">{$lang->total}</th>
        </tr>
    </thead>
    <tbody>
        {$finalized_stats}
    </tbody>
    <tfoot>
        <tr>
            <td style="border-right: 1px solid #EAEDEE;">&nbsp;</td><td style="text-align:center;">{$totals[beforetime]}</td><td style="text-align:center; border-right: 1px solid #EAEDEE;">{$totals[beforetimeperc]}%</td><td style="text-align:center;">{$totals[ontime]}</td><td style="text-align:center; border-right: 1px solid #EAEDEE;">{$totals[ontimeperc]}%</td><td style="text-align:center;">{$totals[late]}</td><td style="text-align:center; border-right: 1px solid #EAEDEE;">{$totals[lateperc]}%</td><td style="text-align:center;">{$totals[finalizedtotal]}</td>
        </tr>
    </tfoot>
</table>
<hr />

<div style="font-weight:bold; margin-top: 20px;">{$lang->finalizedvsnfinalized}</div>
<table class="datatable">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th colspan="2" style="width:18%; text-align:center;">{$lang->finalized}</th>
            <th colspan="2" style="width:18%; text-align:center;">{$lang->notfinalized}</th>
            <th style="width:18%; text-align:center;">{$lang->total}</th>
        </tr>
    </thead>
    <tbody>
        {$general_stats}
    </tbody>
    <tfoot>
        <tr>
            <td style="border-right: 1px solid #EAEDEE;">&nbsp;</td><td style="text-align:center;">{$totals[finalizedtotal]}</td><td style="text-align:center; border-right: 1px solid #EAEDEE;">{$totals[finalizedtotalperc]}%</td><td style="text-align:center;">{$totals[notfinalized]}</td><td style="text-align:center; border-right: 1px solid #EAEDEE;">{$totals[notfinalizedperc]}%</td><td style="text-align:center;">{$totals[total]}</td>
        </tr>
    </tfoot>
</table>
<hr />

<div style="font-weight:bold; margin-top: 20px;">{$lang->reportsdetailedstatus}</div>
<table class="datatable">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th style="width:15%; text-align:center;">{$lang->noproducts}</th>
            <th style="width:15%; text-align:center;">{$lang->nokeycustomers}</th>
            <th style="width:15%; text-align:center;">{$lang->nomarketreport}</th>
            <th style="width:15%; text-align:center;">{$lang->sentreports}</th>
        </tr>
    </thead>
    <tbody>
        {$status_stats}
    </tbody>
</table>
<hr />
<div align="center">
    <p><img src='{$pie->get_chart()}' /></p>
    <p> <img src='{$pie2->get_chart()}' /></p>
</div>
<hr />
<div class="subtitle" style="margin-top:25px;">{$lang->switchtodiffquarter}</div>
<form action="index.php?module=reporting/stats" method="post">
    {$lang->quarter} {$quarters_list}&nbsp;{$lang->year} <select id="year" name="year" tabindex="2"></select>&nbsp;<span id="year_Loading"></span>
    <input type="submit" id="changequarter_Button" value="{$lang->switchquarter}" disabled/>
</form>
