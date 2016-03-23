<script type="text/javascript">
    $(function() {
        var image_name = 'loading-bar.gif';
        $('li[id="widgetinstancelist_{$widget_input['inputChecksum']}_item"]').html("<img style='padding: 5px;' src='" + imagespath + "/" + image_name + "' alt='" + loading_text + "' border='0' />");
        $('li[id="widgetinstancelist_{$widget_input['inputChecksum']}_item"]').load('{$core->settings['rootdir']}/index.php?module=portal/dashboard', 'action=parse_widgetinstance&inputChecksum={$widget_input['inputChecksum']}&id={$widget_input['swgiid']}&dashid={$widget_input[sdid]}');
        $(".ui-dialog-content").dialog("close");
    });
</script>