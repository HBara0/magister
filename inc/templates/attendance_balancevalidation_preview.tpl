<h1>{$lang->validatebalances}</h1>
<form action="index.php?module=attendance/balancesvalidations&amp;action=fixbalances" method="post">
    <table width="100%" class="datatable">
        {$tableheader}
        {$tablerows}
    </table>
    <hr />

    <input type="hidden" value="{$identifier}" name="identifier" id="identifier">
    <fieldset class="altrow">
        <legend class="subtitle">{$lang->choosecorrections}</legend>
        <div style="width:20%; display:inline-block;">{$lang->fixdaystaken}</div>
        <div style="width:70%; padding:3px; display:inline-block;">
            <input type="radio" value="1" name="fixdaysTaken">
            {$lang->yes}
            <input type="radio" value="0" name="fixdaysTaken" checked>
            {$lang->no}</div>
        <div style="width:20%; display:inline-block;">{$lang->fixremprevyear}</div>
        <div style="width:70%; padding:3px; display:inline-block;">
            <input type="radio" value="1" name="fixremainPrevYear">
            {$lang->yes}
            <input type="radio" value="0" name="fixremainPrevYear" checked >
            {$lang->no}</div>
        <div>
            <div style="width:20%; display:inline-block;">{$lang->fixcantake}</div>
            <div style="width:70%; padding:3px; display:inline-block;">
                <input type="radio" value="1" name="fixcanTake">
                {$lang->yes}
                <input type="radio" value="0" name="fixcanTake" checked >
                {$lang->no}</div>
            <div>
                <input type="submit" class="button" value="{$lang->correctbalances}">
            </div>
    </fieldset>

</form>