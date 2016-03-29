<h1>{$lang->lookupflights}</h1>
<form name='do_travelmanager/flights_Form' id="do_travelmanager/lookupflights_Form" method="post" action="index.php?module=travelmanager/lookupflights&amp;action=do_lookupflights">
    <div align="center">
        <table width="80%">
            <tr>
                <td style="width:20%; font-weight: bold;">{$lang->flyfrom}</td><td style="width:40%;">{$flyfrom_field}</td>
                <td style="width:20%; font-weight: bold;">{$lang->flyto}</td><td style="width:40%;">{$flyto_field}</td>
            </tr>
            <tr>
                <td style="width:20%;text-align:left">{$lang->maxrate}</td>
                <td><input type="text" id='maxrate' name="maxrate" accept="numeric" size="4" tabindex="3"/></td>
            </tr>
            <tr>
                <td colspan="4" align="center"><hr /><input type='submit' class='button' value='{$lang->lookupflights}' id='do_travelmanager/lookupflights_Button' /></td>
            </tr>
        </table>
    </div>
</form>