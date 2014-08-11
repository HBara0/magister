<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->requestleave}</title>
        {$headerinc}
    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->viewplan} : {$plan_name}</h3>
            <div id="container" style="width:100%; margin: 0px auto;display:block;">
                {$leave_details}
                {$segment_details}
                {$transportaion_fields}
                <div style=" width:100%; background-color :white ; display: block;">{$segment_expenses} </div>
            </div>

        </td>
    </tr>
    {$footer}
</body>
</html>