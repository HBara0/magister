<div id="quickintro-dialog" title="Quick Intro">
    <p>
    <h3>Welcome to Magister!</h3>
    Please take few minutes to watch this quick introduction video which helps you to handle some of the basic first steps your might need to do.
</p>

<iframe src="https://docs.google.com/file/d/0B8G025lo12QnZVFVS25iYldzY2s/preview" width="640" height="480"></iframe>
</div>
<script>
    $(function() {
        $("#quickintro-dialog").dialog({
            modal: true, closeOnEscape: false, draggable: false, resizable: false, width: 660,
            buttons: {
                Close: function() {
                    $(this).dialog("close");
                }
            }
        }).parent().find('.ui-dialog-titlebar-close').hide();
    });
</script>