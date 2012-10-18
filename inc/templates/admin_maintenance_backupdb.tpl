<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->backupdatabase}</title>
{$headerinc}
</head>
<body>
{$header}
<tr>
{$menu}
<td class="contentContainer">
<h3>{$lang->backupdatabase}</h3>
	<p>{$lang->selectbackuptables}:</p>
	<form id="perform_maintenance/backupdb_Form" name="perform_maintenance/backupdb_Form" action="index.php?module=maintenance/backupdb&amp;action=do_perform_backupdb" method="post">
    {$tables_list}
    <p><input type="submit" id="perform_maintenance/backupdb_Button" value="{$lang->backup}" /> <input type="reset" value="{$lang->reset}" /></p>
    </form>
    <div id="perform_maintenance/backupdb_Results"></div>
</td>
  </tr>
{$footer}
</body>
</html>