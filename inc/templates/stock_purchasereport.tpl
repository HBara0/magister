<html>
<head>
<title>{$core->settings[systemtitle]} | {$profile[companyName]}</title>
{$headerinc}
<style type="text/css">
span.listitem:hover { border-bottom: #CCCCCC solid thin; }
</style>
</head>
<body>
{$header}
<tr>
{$menu}
<td class="contentContainer">
<h3>{$lang->purchasereporttitle}</h3>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
        	<td>
        	{$content}
        	</td>
        </tr>
  </table>
</td></tr>
{$footer}
</body>
</html>