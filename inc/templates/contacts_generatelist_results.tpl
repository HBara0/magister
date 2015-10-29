<div>
    <h2>{$result_title}</h2>
    <table  class="datatable" width="100%" border="0" cellspacing="0" cellpadding="2" id="results_table">
        <thead><tr class="thead">{$results_head}</tr></thead>
        <tbody>{$results_body}</tbody>
    </table>
    <a style="float:right" title="{$lang->exporttoexcel}" onClick ="$('#results_table').tableExport({type:'excel',escape:'false'});"><img src="./images/icons/xls.gif"/></a>
</div>