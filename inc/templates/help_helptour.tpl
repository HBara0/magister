<!-- Start Tour -->
<script>
    $(function() {
        $('#{$this->id}').joyride({
            postRideCallback: function() {
                $('.first-joyride-tips').joyride('destroy');
            },
            autoStart: true,
            'cookieMonster': true, // true/false for whether cookies are used
            'cookieName': '{$this->cookiename}', // choose your own cookie name
            'cookieDomain': false
        });
    });




</script>
<ol id="{$this->id}">
    {$items}
</ol>