<script type="text/javascript">
    $(function() {

        $("select[id^='forecastline_']").each(function() {
            var id = $(this).attr('id').split("_");
            $('input[id=forecastline_nextyear_' + id[2] + '_' + id[3] + ']').val($(this).val());
        });

        $(document).on('change keyup live', "input[id^='forecastline_']", function() {
            var id = $(this).attr('id').split("_");
            var total = 0;
            var monthtotal = 0;
            $('input[id^=forecastline_' + id[1] + '_month]').each(function() {
                if(!jQuery.isEmptyObject(this.value)) {
                    total += parseFloat(this.value);
                }
            });
            $('span[id=total_' + id[1] + ']').text(total.toFixed(2));
            $('input[id^=forecastline_][id$=_month_' + id[3] + ']').each(function() {
                if(!jQuery.isEmptyObject(this.value)) {
                    monthtotal += parseFloat(this.value);
                }
            });
            $('span[id=forecastline_total_' + id[2] + id[3] + ']').text(monthtotal.toFixed(2));
        });

        $(document).on('change', 'input[id^=product_noexception_][id$=output]', function() {
            var id = $(this).attr('id').split("_");
            $('input[id=forecastline_nextyear_' + id[2] + '_pid]').val($('input[id^=product_noexception_' + id[2] + '_id]').val());
        });


        $(document).on('change', 'select[id^=forecastline_]', function() {
            var id = $(this).attr('id').split("_");
            $('input[id=forecastline_nextyear_' + id[2] + '_' + id[3] + ']').val($(this).val());
        });
    });
</script>
<h1>{$lang->quantitiesforecast}<br/><small>{$supplier->get()['companyName']} - {$affiliate->get()['name']}</small></h1>
<form name="perform_grouppurchase/fillforecast_Form" id="perform_grouppurchase/fillforecast_Form" action="#" method="post">
    <input type="hidden" id='spid' name="spid" value="{$forecast_data[spid]}"/>
    <input type="hidden" id='affid' name="affid" value="{$forecast_data[affid]}"/>
    <input type="hidden" id='year' name="year" value="{$forecast_data[year]}"/>
    <input type="hidden" id='uid' name="uid" value="{$uid}"/>
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <thead>
            <tr>
                <td colspan="2"></td>
                <td colspan="{$curryear_countmonths}" class="yellowbackground" style="text-align: center;">
                    {$currentyear}
                </td>
                <td colspan="{$nextyear_countmonths}" class="altrow2" style="text-align: center">
                    {$nextyear}
                </td>
            </tr>
            <tr>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$lang->product}<a href="index.php?module=contents/addproducts&amp;referrer=budgeting" target="_blank"><img src="images/addnew.png" border="0" alt="{$lang->add}"></a></td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$lang->saletype}</td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$mon1}</td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$mon2}</td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$mon3}</td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$mon4}</td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$mon5}</td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$mon6}</td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$mon7}</td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$mon8}</td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$mon9}</td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$mon10}</td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$mon11}</td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left">{$mon12}</td>
                <td class=" border_right" align="center" rowspan="2" valign="top" align="left" style="font-weight:bold">{$lang->total}</td>
            </tr>
        </thead>
        <tbody id="forecastlines_{$rowid}_tbody" style="width:100%;">
            {$forecastlines}
        </tbody>
        <tfoot>
            <tr><td valign="top">
                    <input name="numrows_forecastlines{$rowid}" type="hidden" id="numrows_forecastlines_{$rowid}" value="{$rowid}">
                    <input type="hidden" name="ajaxaddmoredata[affid]" id="ajaxaddmoredata_affid" value="{$forecast_data[affid]}"/>
                    <input type="hidden" name="ajaxaddmoredata[spid]" id="ajaxaddmoredata_spid" value="{$forecast_data[spid]}"/>
                    <img src="./images/add.gif" id="ajaxaddmore_grouppurchase/fillforecast_forecastlines_{$rowid}" alt="{$lang->add}">
                </td></tr>
            <tr>

                <td></td>
                <td class=" border_right" align="center"><span style="font-weight:bold;">{$lang->total}</span></td>
                    {$total_output}
            </tr>
        </tfoot>
    </table>
    <div class="ui-state-highlight ui-corner-all" style="padding:5px; margin-top:10px;;margin-bottom:10px;">
        <input type="checkbox" id="notify" name="notify" value="1"/>{$lang->informconcernedparties}
    </div>
    <input type="submit" id="perform_grouppurchase/fillforecast_Button" value="{$lang->savecaps}" class="button"/>
</form>
<div id="perform_grouppurchase/fillforecast_Results"></div>