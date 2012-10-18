<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->error}</title>
{$redirect}
{$headerinc}
</head>
<body>
    <div align="center">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" id="errorbox">
            <tr><td class="header">{$lang->error}</td></tr>
            <tr>
                <td class="content">
         		{$error_message}
                </td>
            </tr>
       	  <tr><td class="footer"><a href="javascript:history.go(-1);">{$lang->back}</a></td></tr>
        </table>
    </div>
</body>
</html>