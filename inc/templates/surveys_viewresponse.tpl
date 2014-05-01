<head>
    <title>{$core->settings[systemtitle]} | {$survey_details[subject]}</title>
    {$headerinc}
</head>
<body>
    {$header}
<tr>
    {$menu}
    <td class="contentContainer">
        <h3>{$lang->responses} - {$survey_details[subject]}</h3>
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