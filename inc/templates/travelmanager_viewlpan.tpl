<script type="text/javascript">

    $(function() {
        $(document).on('click', 'input[id="confirm_finalize"]', function() {
            $('input[type="submit"][id^="perform_travelmanager/viewplan_Button"]').attr("disabled", !this.checked);
        });
        $(document).on('click', 'button[id="closepage"]', function() {
            window.close();
        });
    });

</script>

{$content}
