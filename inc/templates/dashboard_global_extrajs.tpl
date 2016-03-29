<script type="text/javascript">
    $(function() {
        var image_name = 'loading-bar.gif';
        $('div[id="dashboard_{$dashboard['inputChecksum']}_content"]').html("<img style='padding: 5px;' src='" + imagespath + "/" + image_name + "' alt='" + loading_text + "' border='0' />");
        $('div[id="dashboard_{$dashboard['inputChecksum']}_content"]').load('{$core->settings['rootdir']}/index.php?module=portal/dashboard', 'action=parse_dashboard&inputChecksum={$dashboard[inputChecksum]}&id={$dashboard[sdid]}');
        $(".ui-dialog-content").dialog("close");
    });
</script>