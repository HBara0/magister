<tr id="{$rowid}" class="{$altrow}">
    <td>{$lang->employees} <input type="hidden" name="meeting[attendees][uid][matids]" value="{$matids}"/></td>
    <td>
        <input type="text" id="demo-input-local" name="meeting[attendees][uid][ids]" />
        <script type="text/javascript">
            $(document).ready(function() {
                $("#demo-input-local").tokenInput(
                        "{$core->settings[rootdir]}/search.php?&type=quick&returnType=jsontoken&for=user", {
                            minChars: 2, preventDuplicates: true, method: 'POST', queryParam: 'value', minChars: 2, jsonContainer: null, contentType: "json", prePopulate: [{$eistingattendees}],
                            resultsFormatter: function(item) {
                                if(typeof item.desc != 'undefined') {
                                    return "<li>" + item.value + " <br><small>" + item.desc + "</small></li>";
                                }
                                return "<li>" + item.value + "</li>";
                            },
                            propertyToSearch: "value",
                        }
                );
            });
        </script>
    </td>
</tr>