<tr id="contractsection_title"><td colspan="2" class="subtitle">{$lang->contractualinformation}</td></tr>
<tr id="contractsection_firstsigndate">
    <td>{$lang->initialcontractsign}</td><td><input type="text" id="pickDate_firstsigndate" autocomplete="off" tabindex="2" value="{$entity[contractFirstSigDate_output]}" /><input type="hidden" name="contractFirstSigDate" id="altpickDate_firstsigndate" value="{$entity[contractFirstSigDate]}"/></td>
</tr>
<tr id="contractsection_contractexpirydate">
    <td>{$lang->expirydate}</td><td><input type="text" id="pickDate_contractexpirydate" autocomplete="off" tabindex="2" value="{$entity[contractExpiryDate_output]}" /><input type="hidden" name="contractExpiryDate" id="altpickDate_contractexpirydate" value="{$entity[contractExpiryDate]}"/> <input type="checkbox" id="contractIsEvergreen" name="contractIsEvergreen" value="1"{$contractIsEvergreen_check}> {$lang->contractevergreen}</td>
</tr>
<tr id="contractsection_priornotice">
    <td>{$lang->cancelpriornotice}</td><td><input type="text" id="contractPriorNotice" name="contractPriorNotice" value="{$entity[contractPriorNotice]}"></td>
</tr>
<tr>
    <td  colspan="2">
        <div style="width:100%; height:200px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;" id='coveredcountries_section'>
            <table class="datatable" width="100%">
                <thead>
                <th><input class='inlinefilterfield' type='text' style="width:100%" placeholder="{$lang->coveredcountries}"/></th>
                <th>{$lang->isexclusive}</th>
                <th>{$lang->selectiveproducts}</th>
                </thead>
                <tbody>
                    {$coveredcountries_rows}
                </tbody>
            </table>
        </div>
        <hr />
    </td>
</tr>