<h1>{$lang->addmissingfxrates}</h1>
<form action="#" method="post" id="perform_entities/addmissingrates_Form" name="perform_entities/addmissingrates_Form" enctype="multipart/form-data">
    <table width="100%">
        <tr>
            <td>
                {$lang->fromdate}
            </td>
            <td>
                <input type="text" id="pickDate_from" autocomplete="off" tabindex="1" required="required"/>
                <input type="hidden" name="rate[fromDate]" id="altpickDate_from"  />
            </td>
            <td>
                {$lang->todate}
            </td>
            <td><input type="text" id="pickDate_to" autocomplete="off" tabindex="2"  required="required" />
                <input type="hidden" name="rate[toDate]" id="altpickDate_to" />
            </td>
        </tr>
        <tr> <td colspan="4"><hr></td></tr>
        <tr>
            <td>
                {$lang->sourcecurrencies}
            </td>
            <td>
                {$sourcecurrencies_list}
            </td>
            <td>
                {$lang->tocurrencies}
            </td>
            <td>
                {$tocurrencies_list}
            </td>
        </tr>
    </table>
    <hr>
    <input type="sybmit" class="button" id="perform_entities/addmissingrates_Button"  value='{$lang->submit}' />
    <div id="perform_entities/addmissingrates_Results"></div>
</form>
