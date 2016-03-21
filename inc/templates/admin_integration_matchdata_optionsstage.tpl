<h1>{$lang->matchdata}</h1>
<form action="#" method="post" id="do_integration/matchdata_Form" name="do_integration/matchdata_Form" action="index.php?module=integration/matchdata">
    <input type="hidden" value="preview_datatomatch" name="action" id="action" />
    <table width="100%" class="datatable">
        <tr>
            <td>{$lang->matchwith}</td><td><select name="foreignSystem" id="foreignSystem"><option value="1">Outsys</option><option value="2">Sage Accpac</option><option value="3">Openbravo</option><option value="4">SYSPRO</option><option value="5">Iran ERP</option><option value="6">Sage 1000</option><option value="7">MS Excel</option><option value="8">Sage Evolution</option><option value="9">Sage 100</option><option value="10">Pakistan ERP</option></select></td>
        </tr>
        <tr>
            <td>{$lang->matchitem}</td><td><select id="matchitem" name="matchitem"><option value="products">{$lang->products}</option><option value="suppliers">{$lang->suppliers}</option><option value="customers">{$lang->customers}</option></select></td>
        </tr>
        <tr>
            <td>{$lang->filteraffiliate}</td><td>{$affiliates_list}</td>
        </tr>
        <tr>
            <td>{$lang->filterphrase}</td><td><input type="text" id="filterphrase" name="filterphrase" /></td>
        </tr>
        <tr>
            <td>{$lang->limit}</td><td><input accept='numeric' type="text" id="limitfrom" name="limitfrom" size='3'/> <input type="text" accept='numeric' id="limitnum" name="limitnum"  size='3'/></td>
        </tr>
        <tr><td colspan="2"><input type="submit" class="button" value="{$lang->next}" id="do_integration/matchdata"/></td></tr>
    </table>

</form>