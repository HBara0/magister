<script src="{$core->settings[rootdir]}/js/raphael-2.1.4.min.js" type="text/javascript"></script>
<script src="{$core->settings[rootdir]}/js/justgage.js" type="text/javascript"></script>
<div id="{$divid}"></div>

<script>
    var g = new JustGage({
        id: "{$divid}",
        value: {$leavestat['daysTaken']},
        min: {$leavestat['minimum']},
        max:{$leavestat['canTake']},
        title: "{$leavestat['title']}"
    });
</script>