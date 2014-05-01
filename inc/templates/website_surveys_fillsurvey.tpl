<head>
    <title>{$survey_details[subject]}</title>
    {$headerinc}
</head>
<body>
    <div class="contentContainer" style="margin:auto; -moz-box-shadow:0px 8px 10px 10px #f5f5f5; -webkit-box-shadow:0px 8px 10px 10px #f5f5f5; box-shadow:0px 8px 10px 10px #f5f5f5;">
        <h3>{$survey_details[subject]}</h3>
        <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;">
            <p>{$survey_details[description]}</p>
        </div>

        <form name="perform_surveys/fill_Form" id="perform_surveys/fill_Form" method="post">
            <input type="hidden" value="{$token}" name="token" />
            <input type="hidden" value="{$survey_details[identifier]}" name="identifier" />
            <input type="hidden" value="{$core->input[invitation]}" name="invitation" />
            {$questions_list}
            <hr />
            <input type='button' class='button' value="{$lang->savecaps}" id='perform_surveys/fill_Button'>
        </form>
        <div id="perform_surveys/fill_Results"></div>
    </div>
</body>
</html>