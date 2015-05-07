<div id="popup_clonebrandprod" title="{$lang->cloneentitybrand}">
    <form name='add_profiles/brandprofile_Form' id="add_profiles/brandprofile_Form" method="post" >
        <input type="hidden" id="action" name="action" value="do_clonebrand" />
        <table width="100%">
            <tr>
                <td>{$lang->brandname}</td>
                <td><input type='text' id='brands_1_autocomplete' name='newbrand' value=""/><input type="hidden" size="3" id="brands_1_id_output" value="" disabled/><input type='hidden' id='brands_1_id' name='brand' value="" /><div id='searchQuickResults_brands_1' class='searchQuickResults' style='display:none;'></div></td>
            </tr>
            <tr>
                <td> {$lang->endproductname}</td><td><input type='text' id='endproducttypes_1_autocomplete' value="{$endproduct_type->get_displayname()}"/><input type="hidden" size="3" id="endproducttypes_1_id_output" value="{$endproduct_type->eptid}" disabled/><input type='hidden' id='endproducttypes_1_id' name='endproduct' value="{$endproduct_type->eptid}" /><div id='searchQuickResults_endproducttypes_1' class='searchQuickResults' style='display:none;'></div>
            </tr>
            <tr>
                <td> {$lang->cusomter}</td><td><input type='text' id='customer_1_autocomplete' value="{$customer->get_displayname()}"/><input type="hidden" size="3" id="customer_1_id_output" value="{$customer->eid}" disabled/><input type='hidden' id='customer_1_id' name='customer' value="{$customer->eid}" /><div id='searchQuickResults_customer_1' class='searchQuickResults' style='display:none;'></div></td>
            </tr>
            <tr>
                <td>
                    <table class="datatable">
                        <thead>
                            <tr>
                                <th>Select</th><th>{$lang->chemicalsubs}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$chemfuncobj_clone}
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="datatable">
                        <thead>
                            <tr>
                                <th>Select</th><th>{$lang->products}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$products_clone}
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="datatable">
                        <thead>
                            <tr>
                                <th>Select</th><th>{$lang->ingredients}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$ingredients_clone}
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <input type='button' class='button' value='{$lang->save}' id='add_profiles/brandprofile_Button' />
    </form>
    <div id="add_profiles/brandprofile_Results" ></div>
</div>