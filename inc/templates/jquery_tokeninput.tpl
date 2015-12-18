<script type="text/javascript">
    $(document).ready(function() {
        $('input[id^="tokeninput_{$tokenfields}_input"]').each(function(i, obj) {
            var id = $(this).attr("id").split("_");
            $(this).tokenInput(
                    "{$core->settings[rootdir]}/search.php?&type=quick&returnType=jsontoken&for=" + id[1], {
                minChars: 2, preventDuplicates: true, method: 'POST', queryParam: 'value', minChars: 2, jsonContainer: null, contentType: "json", prePopulate: [{$eistingattendees}],
                resultsFormatter: function(item) {
                    if(typeof item.desc != 'undefined') {
                        return "<li>" + item.value + " <br><small>" + item.desc + "</small></li>";
                    }
                    return "<li>" + item.value + "</li>";
                },
                tokenFormatter: function(item) {
                    var output = "<li>" + item.value;
                    if(typeof item.desc != 'undefined') {
                        output += " <br><small>" + item.desc + "</small>";
                    }
                    if(typeof item.inputname != 'undefined') {
                        output += "<input type='hidden' name='meeting[attendees][uid][" + item.id + "][id] value=\"" + item.id + "\"";
                    }
                    if(typeof item.existinginput != 'undefined') {
                        output += item.existinginput;
                    }
                    return output + "</li>";
                },
                propertyToSearch: "value",
            }
            );
        });
    });
</script>