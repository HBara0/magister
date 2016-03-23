<script>
    $(function() {
        $(document).on('change', '#brands_1_id_output', function() {
            if($('#brands_1_id_output').val() > 0) {
                $('input[id="customer_1_autocomplete"]').attr('disabled', 'disabled');
                $('input[id="customer_1_id"]').val('0');
                $('input[id="customer_1_autocomplete"]').val('');
            }
            else {
                $('input[id="customer_1_autocomplete"]').removeAttr('disabled');
            }
        });
        $(document).on('change', 'input[id ="brands_1_autocomplete"]', function() {
            $('input[id="customer_1_autocomplete"]').removeAttr('disabled');
        });
    });
</script>
<h1>{$page_title_header}<small><br />{$customername}</small></h1>
<div style="display:inline-block;width:45%">{$clone_button}</div><div style="width:50%;display:inline-block; color: #91B64F; text-align: right;">{$reviewed}</div>
<div style="display:inline-block;width:45%"></div>
<div style="width:50%;display:inline-block;">
    <div id="perform_profiles/brandprofile_Results"></div>
    <form action="#" method="post" id="perform_profiles/brandprofile_Form" id="perform_profiles/brandprofile_Form">
        <input type="hidden" name="ebpid" value="{$core->input[ebpid]}"/>
        {$reviewbtn}
    </form>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    {$endproducts_list}
    {$chemsubstance_list}
    {$products_list}
    {$ingredients_list}
</table>

{$pop_clone}
