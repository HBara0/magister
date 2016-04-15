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
        {$comparisonstudy}
        {$aro_audittrail}
        {$orderummary}
        {$totalfunds}
    </form>
    <div id="perform_aro/managearodouments_Results"></div>
    <hr />
    {$takeactionpage}
    {$approvalchain}
</div>

{$helptour_output}


