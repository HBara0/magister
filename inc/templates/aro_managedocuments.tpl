<h1>Aro Document</h1>
<div style="display:inline-block;width:60%">
    {$arostatus}
    {$arodocument_header}  {$rejected_watrermark}</div>
<div style="display:inline-block;width:30%">
    {$postatus}
</div>
{$revisiedaro_output}
<div class="accordion">
    <form name="perform_aro/managearodouments_Form" id="perform_aro/managearodouments_Form"  action="#" method="post">
        {$aro_managedocuments_orderident}
        {$partiesinformation}
        {$aro_ordercustomers}
        {$aro_netmarginparms}
        {$aro_productlines}
        {$actualpurchase}
        {$currentstock}
        {$aro_audittrail}
        {$orderummary}
        {$totalfunds}
        {$approvalchain}
        <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;width:30%">
            {$lang->finalizedemail}
        </div>
        <input type="checkbox" value="1" name="isFinalized" {$checked[aroisfinalized]}/>{$lang->finalize}<br/><br/>
        <input type="submit" class="button" id="perform_aro/managearodouments_Button" value="{$lang->savecaps}"/>
        {$deletebutton}
    </form>
    <div id="perform_aro/managearodouments_Results"></div>
    <hr />
    {$takeactionpage}
</div>
{$helptour_output}
