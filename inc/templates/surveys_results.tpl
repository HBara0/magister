<script>
    $(function() {
        $("[id^='getquestionresponses_']").click(function() {
            if(sharedFunctions.checkSession() == false) {
                return;
            }
            var id = $(this).attr("id").split("_");

            sharedFunctions.requestAjax("post", "index.php?module=surveys/viewresults&action=get_questionresponses", "question=" + id[1] + "&identifier=" + id[2], 'questionresponses_results_' + id[1], 'questionresponses_results_' + id[1], 'html');
        });

        $("[id='crosstabulation']").click(function() {
            if(sharedFunctions.checkSession() == false) {
                return;
            }

            sharedFunctions.requestAjax("post", "index.php?module=surveys/viewresults&action=get_crosstabulation", "identifier=" + $('[name="identifier"]').val(), 'crosstabulation_results', 'crosstabulation_results', 'html');
        });
    });
</script>
<h1>{$survey[subject]}</h1>
{$questionsstats}
{$responses}
<div style="display:inline-block; width: 12%; margin:5px; text-align:right; float:right;">
    <div id="perform_surveys/viewresults_Results">
        <form action="#" method="post" id="perform_surveys/viewresults_Form" name="perform_surveys/viewresults_Form">
            <input type="hidden" value="{$core->input[identifier]}" name="identifier">
            <input name="action" value="sendreminders" type="hidden" />
            <input value="{$lang->sendreminders}" type="button" id="perform_surveys/viewresults_Button" class="button" {$display[sendreminders]}/>
        </form>
    </div>
</div>
{$pendingresponses}
{$invitations}
{$shareresultswith}
