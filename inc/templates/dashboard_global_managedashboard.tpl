<script type="text/javascript">
    $("a[id^='select_'][id$='_widgettype']").click(function () {
        var id = $(this).attr("id").split("_");
        if(!id[1]) {
            alert('Error In Widget Type');
            return;
        }
        var wid = id[1];
        if(sharedFunctions.checkSession() == false) {
            alert('Error In Widget Type');
            return;
        }
        var dashid = '';
        if($("input[id='dasboard_id']").val()) {
            dashid = $("input[id='dasboard_id']").val();
        }
        var inputChecksum = '';
        if($("input[id='dasboard_inputChecksum']").val()) {
            inputChecksum = $("input[id='dasboard_inputChecksum']").val();
        }
        if(dashid == '' && inputChecksum == '') {
            alert('Error In Widget Type');
            return;
        }
        var url = "index.php?module=portal/dashboard&action=populate_widgetsettings";
        sharedFunctions.requestAjax("post", url, "&wid=" + wid + "&inputChecksum=" + inputChecksum + "&dashid=" + dashid, "widgetsettings_result", "widgetsettings_result", 'html');
    });
</script>
{$widget_list}
<h2>{$dashboard_title}</h2>
<br><hr>
{$dashboard_output}