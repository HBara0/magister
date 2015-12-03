<head>
    <title>{$core->settings[systemtitle]} | {$survey_details[subject]}</title>
    {$headerinc}
</head>
<body>
    {$header}
<tr>
    {$menu}
    <td class="contentContainer">
        <h1>{$lang->responses} - {$survey_details[subject]}</h1>
        <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;">
            {$scores}
        </div>
        <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;">
            <p>{$survey_details[description]}</p>
            {$associations_list}
        </div>
        {$questions_list}
    </td>
</tr>
{$footer}
</body>
</html>