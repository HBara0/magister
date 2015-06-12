<div id="popup_clonebrandprod" title="{$lang->cloneentitybrand}">
    <form name='add_profiles/brandprofile_Form' id="add_profiles/brandprofile_Form" method="post" >
        <input type="hidden" id="action" name="action" value="do_clonebrand" />
        <table width="100%">
            <tr>
                <td width="30%">{$lang->brandname}</td>
                <td><input type='text' id='brands_1_autocomplete' name='newbrand' value="" style="width:65%;"/><input type="hidden" size="3" id="brands_1_id_output" value="" disabled/><input type='hidden' id='brands_1_id' name='brand' value="" /><div id='searchQuickResults_brands_1' class='searchQuickResults' style='display:none;'></div></td>
            </tr>
            <tr>
                <td width="30%">{$lang->customer}</td><td><input type='text' id='customer_1_autocomplete' value="{$customer->get_displayname()}" style="width:65%;"/><input type="hidden" size="3" id="customer_1_id_output" value="{$customer->eid}" disabled/><input type='hidden' id='customer_1_id' name='customer' value="{$customer->eid}" /><div id='searchQuickResults_customer_1' class='searchQuickResults' style='display:none;'></div></td>
            </tr>
            <tr>
                <td width="30%">{$lang->endproductname}</td><td><input type='text' id='endproducttypes_1_autocomplete' value="{$endproduct_type->get_displayname()}" style="width:65%;"/><input type="hidden" size="3" id="endproducttypes_1_id_output" value="{$endproduct_type->eptid}" disabled/><input type='hidden' id='endproducttypes_1_id' name='endproduct' value="{$endproduct_type->eptid}" /><div id='searchQuickResults_endproducttypes_1' class='searchQuickResults' style='display:none;'></div>
            </tr>
            <tr>
                <td>{$lang->characterstics}</td><td>{$characteristics_list}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="width:100%; height:150px; overflow:auto; vertical-align:top;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr class="thead">
                                    <th>{$lang->chemicalsubs}</th>
                                </tr>
                                <tr>
                                    <th><input class='inlinefilterfield' type='text' value='' style="width: 95%" placeholder="{$lang->chemicalsubs}"/></th>
                                </tr>
                            </thead>
                            <tbody id="profmkdchemical_tbody"  class="{$altrow_class}">
                                {$chemfuncobj_clone}
                                {$profiles_michemfuncproductentry_rows}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">
                                        <img src="./images/add.gif" id="ajaxaddmore_profiles/brandprofile_profmkdchemical" alt="{$lang->add}">
                                        <input id="numrows_profmkdchemical" name="numrows_profmkdchemical" type="hidden" value="{$mkdchem_rowid}">
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="width:100%; height:150px; overflow:auto; vertical-align:top;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr class="thead">
                                    <th>{$lang->products}</th>
                                </tr>
                                <tr>
                                    <th><input class='inlinefilterfield' type='text' value='' style="width: 95%" placeholder="{$lang->products}"/></th>
                                </tr>
                            </thead>
                            </thead>
                            <tbody id="profmkdproduct_tbody"  class="{$altrow_class}">
                                {$products_clone}
                                {$profiles_minproductentry_rows}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">
                                        <img src="./images/add.gif" id="ajaxaddmore_profiles/brandprofile_profmkdproduct" alt="{$lang->add}">
                                        <input id="numrows_profmkdproduct" name="numrows_profmkdproduct" type="hidden" value="{$mkdprod_rowid}">
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="width:100%; height:150px; overflow:auto; vertical-align:top;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr class="thead">
                                    <th>{$lang->ingredients}</th>
                                </tr>
                                <tr>
                                    <th><input class='inlinefilterfield' type='text' value='' style="width: 95%" placeholder="{$lang->ingredients}"/></th>
                                </tr>
                            </thead>
                            <tbody id="profmkdbasicing_tbody"  class="{$altrow_class}">
                                {$ingredients_clone}
                                {$profiles_mibasicingredientsentry_rows}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">
                                        <img src="./images/add.gif" id="ajaxaddmore_profiles/brandprofile_profmkdbasicing" alt="{$lang->add}">
                                        <input id="numrows_profmkdbasicing" name="numrows_profmkdbasicing" type="hidden" value="{$mkdbing_rowid}">
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
        <input type='button' class='button' value='{$lang->savecaps}' id='add_profiles/brandprofile_Button' />
    </form>
    <div id="add_profiles/brandprofile_Results" ></div>
</div>