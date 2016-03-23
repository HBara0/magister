<h1>{$lang->trialbalance}</h1>
<form action="index.php?module=finance/integration_trialbalance" method="post" target="_blank">
    <input type="hidden" name='action' value='viewtb'>
    <div style="width: 10%; display: inline-block;">{$lang->affiliate}</div><div style="width: 90%; display: inline-block;">{$selectlists[organisation]}</div>
    <div style="width: 10%; display: inline-block;">{$lang->fromdate}</div><div style="width: 90%; display: inline-block;">
        <input type="text" id="pickDate_from" autocomplete="off" tabindex="1" /> <input type="hidden" name="fromDate" id="altpickDate_from" />
    </div>
    <div style="width: 10%; display: inline-block;">{$lang->todate}</div><div style="width: 90%; display: inline-block;">
        <input type="text" id="pickDate_to" autocomplete="off" tabindex="2" /> <input type="hidden" name="toDate" id="altpickDate_to" />
    </div>
</p>
<p>
<hr />
<input type="submit" value="{$lang->generate}" class="button main"> <input type="reset" value="{$lang->reset}" class="button">
</p>
</form>