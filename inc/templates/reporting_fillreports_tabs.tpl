<link href="{$core->settings[rootdir]}/css/rateit.min.css" rel="stylesheet" type="text/css">
<script src="{$core->settings[rootdir]}/js/fillreport.js" type="text/javascript"></script>
<script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
<script>
    $(function() {
        var tabs = $("#reporttabs").tabs();
        var tabcounter = tabs.find(".ui-tabs-nav").find('li').length + 1; //find the  lenght of li tabs and increment by 1
    {$header_ratingjs}
        $(document).on('click', '#previewed_button', function() {
            $('input[id="previewed_value"]').each(function(i, obj) {
                $(obj).val('1');
            });
            $('input[id="save_productsactivity_reporting/fillreport_Button"]').click();
            setTimeout(function() {
                $('input[id="save_marketreport_reporting/fillreport_Button"]').click()
            }, 2000
                    );
            setTimeout(function() {
                $('#previewed_value').each(function(i, obj) {
                    $(obj).val('');
                });
            }, 4000
                    );
        });
        $(document).on('change', "input[id^='chemicalproducts'][id$='_autocomplete']", function() {
            var id = $(this).attr("id").split("_");
            if(jQuery.isNumeric($('input[id=chemicalproducts_' + id[1] + '_id]').val())) {
                $("div[id='removerow_div']").hide();
            }
        });

        $(document).on('click', "img[id='removerow']", function() {
            $(this).closest("tr").remove();
        });
    });
</script>
<h1>{$lang->reportdetails}<div style="font-style:italic; font-size:12px; color:#888;">Q{$core->input[quarter]} {$core->input[year]} / {$core->input[supplier]} - {$core->input[affiliate]}</div></h1>
<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p><strong>Welcome to the new QR filling process; you no longer need to go back and forth in pages, simply switch tabs.<br />
            The market report now has a dedicated section for competition, please make sure to use it.</strong></div>
<div>{$supp_auditor_output}</div>
<div id="reporttabs">
    <ul>
        <li><a href="#reporttabs-1">{$lang->productactivitydetails}</a></li>
        <li><a href="#reporttabs-2">{$lang->marketreport}</a></li>
        <li><a href="#reporttabs-3">{$lang->preview}</a></li>

    </ul>

    <div id="reporttabs-1">
        {$productsactivitypage}
    </div>
    <div id="reporttabs-2">
        {$marketreportpage}
    </div>
    <div id="reporttabs-3">
        <iframe id="preview_iframe" src="" width="100%" height="1000px">
        </iframe>
    </div>
</div>
<table>
    <tr><td></td><td colspan="3">Product Activity Status:<div id="save_productsactivity_reporting/fillreport_Results"></div></td></tr>
    <tr><td></td><td colspan="3">Market Report Status:            <div id="save_marketreport_reporting/fillreport_Results"></div></td></tr>
</table>

{$addproduct_popup}