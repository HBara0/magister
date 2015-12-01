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
<input type="text"   id="reservationfacilities{$extraid}_autocomplete" data-autocompletefilters="loacationLat,loacationLong{$extra_inputids}" autocomplete="false" tabindex="1" value="{$facilityname}" required="required"/>
<input type="hidden" id="loacationLat"  value="" name="loacationLat"/>
<input type="hidden" id="loacationLong"  value="" name="loacationLong"/>
<input type='hidden' id='reservationfacilities{$extraid}_id'  name="{$facinputname}" value="{$facilityid}"/>
<input type='hidden' id='reservationfacilities{$extraid}_id_output' name="" value="" disabled/>
<a href="index.php?module=facilitymgmt/facilitiesschedule"><img src="./images/icons/calendar.gif" alt='{$lang->viewschedule}'> {$lang->viewschedule}</a>