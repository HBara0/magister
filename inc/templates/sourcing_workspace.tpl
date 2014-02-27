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
    <h3> Workspace</h3>
  
    <div style="display: block;">
                <div style="display:inline-block;">Current Period </div>
        <div style="display:inline-block;">{$kpipercentage[current]} {$trend_output}</div>
    </div>
   
    <div style="display: block;">
                    <div style="display:inline-block;">Previous Month </div>
            <div style="display:inline-block;">{$kpipercentage[last]}</div>
           </div>
    </body>
</html>