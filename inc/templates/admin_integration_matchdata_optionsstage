<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->matchintegrationdata}</title>
{$headerinc}
</head>
<body>
{$header}
<tr>
{$menu}
<td class="contentContainer">
	<h3>{$lang->matchintegrationdata}</h3>
	<form action="#" method="post" id="do_integration/matchdata_Form" name="do_integration/matchdata_Form" action="index.php?module=integration/matchdata">
   		<input type="hidden" value="preview_datatomatch" name="action" id="action" />
        <table width="100%" class="datatable">
        	<tr>
            	<td>{$lang->matchwith}</td><td><select name="foreignSystem" id="foreignSystem"><option value="1">Outsys</option><option value="2">Sage Accpac</option></select></td>
            </tr>
            <tr>
            	<td>{$lang->matchitem}</td><td><select id="matchitem" name="matchitem"><option value="products">{$lang->products}</option><option value="suppliers">{$lang->suppliers}</option></select></td>
            </tr>
            <tr>
            	<td>{$lang->filteraffiliate}</td><td>{$affiliates_list}</td>
            </tr>
            <tr>
            	<td>{$lang->filterphrase}</td><td><input type="text" id="filterphrase" name="filterphrase" /></td>
            </tr>
            <tr><td colspan="2"><input type="submit" class="button" value="{$lang->next}" id="do_integration/matchdata_Button"/></td></tr>
        </table>
       
    </form>
</td>
  </tr>
{$footer}
</body>
</html>