<script src="{$core->settings[rootdir]}/js/generatereport.js" type="text/javascript"></script>
<h1>{$lang->generatereport}</h1>
<div align="center">
    <span class="subtitle">{$message}</span>
    <form name="do_reporting/generatereport_Form" id="do_reporting/generatereport_Form" method="post" action="index.php?module=reporting/preview&amp;referrer=generate">
        <table width="80%" cellspacing="0">
            <tr>
                <td style="width: 50%; border-right: 1px solid #E1F2D0; text-align:center; padding: 10px;">
                    <input type="radio" name="generateType" id="generateType" value="1" tabindex="1" checked> {$lang->reportspecificsupplier}
                </td>
                <td style="text-align:center; padding: 10px;"><input type="radio" name="generateType" id="generateType2" value="2" tabindex="2"> {$lang->reportspecificaffiliate}</td>
            </tr>
            <tr>
                <td style="border-right: 1px solid #E1F2D0; text-align: center; vertical-align:top; padding: 10px;">
                    {$lang->quarter} <select id="quarter" name="quarter" tabindex="3">
                        <option value="0"></option>
                        <option value="1"{$quarter_selected[1]}>1</option>
                        <option value="2"{$quarter_selected[2]}>2</option>
                        <option value="3"{$quarter_selected[3]}>3</option>
                        <option value="4"{$quarter_selected[4]}>4</option>
                    </select>
                </td>
                <td style="text-align: center; vertical-align:top; padding: 10px;">
                    {$lang->year} <select id="year" name="year" tabindex="4">{$years_list}
                    </select> <span id="years_Loading"></span>
                </td>
            </tr>
            <tr>
                <td style="border-right: 1px solid #E1F2D0; text-align: center; vertical-align:top; padding: 10px;">
                    <span class="subtitle">{$lang->affiliate}</span> <span id="affiliateslist_Loading"></span><br />
                    <select id="affid" name="affid[]" tabindex="5" multiple size="5">
                    </select>
                </td>
                <td style="text-align: center; vertical-align:top; padding: 10px;">
                    <span class="subtitle">{$lang->supplier}</span> <span id="supplierslist_Loading"></span><br />
                    <select id="spid" name="spid" tabindex="6">{$available_suppliers}
                    </select>
                </td>
            <tr><td colspan="2" align="center"><hr /></td></tr>
            <tr>
                <td style="border-right: 1px solid #E1F2D0; vertical-align:top; padding-left: 20%;">
                    {$lang->includekeycustomers}
                </td>
                <td>
                    <input type="radio" name="incKeyCustomers" id="incKeyCustomers" value="1" tabindex="9" checked> Yes <input type="radio" name="incKeyCustomers" id="incKeyCustomers2" value="0" tabindex="10"> No
                </td>
            </tr>
            <tr>
                <td style="border-right: 1px solid #E1F2D0; vertical-align:top; padding-left: 20%;">
                    {$lang->includemarketreport}
                </td>
                <td>
                    <input type="radio" name="incMarketReport" id="incMarketReport" value="1" tabindex="13" checked> Yes <input type="radio" name="incMarketReport" id="incMarketReport2" value="0" tabindex="14"> No
                </td>
            </tr>
            <tr><td colspan="2" align="center"><hr /></td></tr>
            <tr><td colspan="2" align="center" id="buttons_row" style="display:none;"><input type="submit" value="{$lang->generate}" class="button main"> <input type="reset" value="{$lang->reset}" class="button"></td></tr>
        </table>
    </form>
</div>
