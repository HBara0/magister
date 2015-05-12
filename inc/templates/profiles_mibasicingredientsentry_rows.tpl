<div id='prof_mkd_basicingredients' style="display: {$css[display][basicingsubfield]};">
    <table  width="100%">
        <tr id="profmkdbasicing_{$mkdbing_rowid}">
            <td style="border-bottom:2px solid #CCC; margin-bottom: 5px;">
                <table width="100%">
                    {$profiles_mibasicingredientsentry_row}
                    <tbody id="profmkdbasicing_tbody"  class="{$altrow_class}">
                        {$profiles_mibasicingredientsentry_rows}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">
                                <img src="./images/add.gif" id="ajaxaddmore_{$module}/{$modulefile}_profmkdbasicing" alt="{$lang->add}">
                                <input id="numrows_profmkdbasicing" name="numrows_profmkdbasicing" type="hidden" value="{$mkdbing_rowid}">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>
</div>