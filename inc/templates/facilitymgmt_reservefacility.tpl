<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->reservefacility}</title>
        {$headerinc}
        <script>
            $(function () {
                if(navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(fillvalue);
                }
            });
            function fillvalue(position) {
                $('input[id="loacationLat"]').val(position.coords.latitude);
                $('input[id="loacationLong"]').val(position.coords.longitude);

            }
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->reservefacility}</h1>
            <form name="perform_facilitymgmt/reserve_Form" id="perform_facilitymgmt/reserve_Form" action="#" method="post">
                <table >
                    <tr><td></td><td>
                            <input type="text" id="pickDate_reserveFrom" autocomplete="off" tabindex="1" value="" required="required"/>
                            <input type="hidden" name="reserveFrom" id="altpickDate_reserveFrom" value="" /></td>
                        </td></tr>
                    <tr><td></td><td>
                            <input type="text" id="pickDate_reserveTo" autocomplete="off" tabindex="1" value="" required="required"/>
                            <input type="hidden" name="reserveTo" id="altpickDate_reserveTo" value="" /></td>
                        </td></tr>
                    <tr><td style="width: 20%">{$lang->searchfacility}</td><td style="width: 30%">
                            <input type="text"   id="reservationfacilities_{$sequence}_autocomplete" autocomplete="false" tabindex="1" value="" required="required"/>
                            <input type="hidden" id="loacationLat"  value="" name="loacationLat"/>
                            <input type="hidden" id="loacationLong"  value="" name="loacationLong"/>
                            <input type='hidden' id='reservationfacilities_{$sequence}_id'  name="" value=""/>
                            <input type='hidden' id='reservationfacilities_{$sequence}_id_output' name="" value="" disabled/>
                        </td></tr>
                </table>
                <input type='submit' style="cursor: pointer;display:none" class='button' value="{$lang->savecaps}" id='perform_facilitymgmt/reserve_Button'>
            </form>
            <div id="perform_facilitymgmt/reserve_Results"></div>
        </td>
    </tr>
    {$footer}
</body>
</html>