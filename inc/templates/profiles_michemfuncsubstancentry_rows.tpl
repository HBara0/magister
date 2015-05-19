<div id='prof_mkd_chemsubfield' style="display:{$css[display][chemsubfield]};">
    <table  width="100%">
        <tr id="profmkdchemical_{$mkdchem_rowid}">
            <td style="border-bottom:2px solid #CCC; margin-bottom: 5px;">
                <table width="100%">
                    {$profiles_michemfuncproductentry_row}
                    <tbody id="profmkdchemical_tbody"  class="{$altrow_class}">
                        {$profiles_michemfuncproductentry_rows}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                                <img src="./images/add.gif" id="ajaxaddmore_{$module}/{$modulefile}_profmkdchemical" alt="{$lang->add}">
                                <input id="numrows_profmkdchemical" name="numrows_profmkdchemical" type="hidden" value="{$mkdchem_rowid}">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>
</div>