<head>
<title>{$core->settings[systemtitle]} | {$lang->fillsurvey}</title>
{$headerinc}
</head>
<body>
{$header}
<tr>
{$menu}
    <td class="contentContainer">
        <h3>{$lang->titletracking}</h3>
  <table class="datatable">
            <tr>
                <th>{$lang->assets}</th>
                <th>{$lang->location}</th>
            </tr>
            
         {$assets_assetstracking_row}
        </table>

    </td>
</tr>
{$footer}
</body>
</html>