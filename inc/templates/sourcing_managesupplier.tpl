<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->managesuppliers}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>{$menu}
        <td class="contentContainer"><h1>{$lang->$actiontype}</h1>
                {$sourcing_managesupplier_blhistory}
            <form name="perform_sourcing/managesupplier_Form" action="{$_SERVER[QUERY_STRING]}" method="post" id="perform_sourcing/managesupplier_Form" >
                <input type="hidden" value="do_{$actiontype}page" name="action" id="action" />
                <input name="supplier[ssid]" type="hidden" value="{$supplierid}" />
                <div style="display:table; border-collapse:collapse; width:100%;">
                    <div style="display:table-row;">
                        <div class="thead" style="display:table-cell; width:20%;">{$lang->generalinfo}</div>
                        <div class="thead" style="display:table-cell; width:80%;"></div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;"><strong>{$lang->companyname}</strong></div>
                        <div style="display: table-cell; padding:5px;">
                            <input id="companyName" name="supplier[companyName]" type="text" value="{$supplier[details][companyName]}" class='inlineCheck' size="30" />
                            &nbsp;{$lang->abbreviation}
                            <input name="supplier[companyNameAbbr]" type="text" value="{$supplier[details][companyNameAbbr]}" size="15" />
                            <div id="companyName_inlineCheckResult" style="paddinng:5px;" class="red_text"></div>
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell; vertical-align:middle;">{$lang->product}</div>
                        <div style="display: table-cell; padding:5px;">{$product_list}</div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">{$lang->country}</div>
                        <div style="display: table-cell; padding:5px;">{$countries_list}</div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">{$lang->city}</div>
                        <div style="display: table-cell; padding:5px;">
                            <input type="text" id="city" value="{$supplier[details][city]}" name="supplier[city]" />
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">{$lang->postcode}</div>
                        <div style="display: table-cell; padding:5px;">
                            <input type="text" value="{$supplier[details][postCode]}" name="supplier[postCode]" />
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">{$lang->address1}</div>
                        <div style="display: table-cell; padding:5px;">
                            <input type="text" value="{$supplier[details][addressLine1]}" name="supplier[addressLine1]" />
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">{$lang->address2}</div>
                        <div style="display: table-cell; padding:5px;">
                            <input type="text" value="{$supplier[details][addressLine2]}" name="supplier[addressLine2]" />
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">{$lang->buildingname}</div>
                        <div style="display: table-cell; padding:5px; ">
                            <input type="text"  value="{$supplier[details][building]}" name="supplier[building]" />
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">{$lang->pobox}</div>
                        <div style="display: table-cell; padding:5px;">
                            <input size="10" type="text"  value="{$supplier[details][poBox]}" name="supplier[poBox]" />
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">{$lang->telephone}</div>
                        <div style="display: table-cell; padding:5px;"> +
                            <input type="text" id="phone1_intcode" name="supplier[phone1][intcode]" value="{$supplier[details][phone1][0]}" size="3" maxlength="3" accept="numeric" />
                            <input type="text" id="phone1_areacode" name="supplier[phone1][areacode]" value="{$supplier[details][phone1][1]}" size='4' maxlength="4" accept="numeric" />
                            <input type="text" id="phone1_number" name="supplier[phone1][number]" value="{$supplier[details][phone1][2]}" accept="numeric"  />
                            <br />
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">{$lang->telephone} 2</div>
                        <div style="display: table-cell; padding:5px;"> +
                            <input type="text" id="phone2_intcode" name="supplier[phone2][intcode]" value="{$supplier[details][phone2][0]}" size="3" maxlength="3" accept="numeric" />
                            <input type="text" id="phone2_areacode" name="supplier[phone2][areacode]" value="{$supplier[details][phone2][1]}" size='4' maxlength="4" accept="numeric" />
                            <input type="text" id="phone2_number" name="supplier[phone2][number]" value="{$supplier[details][phone2][2]}" accept="numeric"  />
                            <br />
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">{$lang->fax}</div>
                        <div style="display: table-cell; padding:5px;"> +
                            <input type="text" id="fax_intcode" name="supplier[fax][intcode]" size="3" maxlength="3" accept="numeric" value="{$supplier[details][fax][0]}"/>
                            <input type="text" id="fax_areacode" name="supplier[fax][areacode]" size='4' maxlength="4" accept="numeric" value="{$supplier[details][fax][1]}" />
                            <input type="text" id="fax_number" name="supplier[fax][number]" accept="numeric" value="{$supplier[details][fax][2]}" />
                            <br />
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">{$lang->email}</div>
                        <div style="display: table-cell; padding:5px; ">
                            <input accept="email" type="text" value="{$supplier[details][mainEmail]}" id="mainEmail" name="supplier[mainEmail]" />
                            <span id="mainEmail_Validation"></span> </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">{$lang->website}</div>
                        <div style="display: table-cell; padding:5px;">
                            <input type="text" value="{$supplier[details][website]}" name="supplier[website]" />
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display:table-cell;">{$lang->selectentity}</div>
                        <div style="display:table-cell; padding:5px;">
                            <input id="supplier_1_autocomplete" autocomplete="off" type="text" value="{$supplier[relatedsupplier]}">
                            <input id="supplier_1_id" name="supplier[eid]" value="{$supplier[eid]}" type="hidden">
                            <div id="searchQuickResults_supplier_1" class="searchQuickResults" style="display:none;"></div>
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div class="thead" style="display:table-cell;">{$lang->activityarea}</div>
                        <div class="thead" style="display:table-cell;"></div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell; width:1px;"></div>
                        <div style="display: table-cell; width:100%;">
                            <div align="left" style="width:100%; margin-top:10px; margin-bottom:15px; height: 200px; overflow:auto; display:inline-block; vertical-align:top;">
                                <table width="100%"  class="datatable" border="0" cellspacing="2" cellpadding="1">
                                    {$activityarea_list_row}
                                </table>
                            </div>
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div class="thead" style="display:table-cell;">{$lang->chemicalproducts}
                        </div>
                        <div class="thead" style="display:table-cell;"></div>
                    </div>
                    <div style="display:table-row; vertical-align:top;">
                        <div style="display: table-cell; padding:5px;text-align: center;font-weight: bold;vertical-align: middle">{$lang->producer}</div>
                        <div style="display: table-cell; padding:5px;">
                            <table>
                                <tbody  >
                                    <tr>
                                        <td>
                                            <input type="text" id="tokeninput_{$tokenfields}_1_input" name="chemicals[p]" />
                                            {$prodinput}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div style="display:table-row; vertical-align:top;">
                        <div style="display: table-cell; padding:5px;text-align: center;font-weight: bold;vertical-align: middle">{$lang->trader}</div>
                        <div style="display: table-cell; padding:5px;">
                            <table>
                                <tbody >
                                    <tr>
                                        <td>
                                            <input type="text" id="tokeninput_{$tokenfields}_2_input" name="chemicals[t]" />
                                            {$tradinput}
                                        </td>
                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <div style="display:table-row; vertical-align:top;">
                        <div style="display: table-cell; padding:5px;"> <a href='#createchemical_id' id='addnew_sourcing/managesupplier_chemical'><img src='images/addnew.png' border='0' alt='{$lang->add}' /></a></div>
                    </div>
                    <div style="display:table-row;">
                        <div class="thead" style="display:table-cell;">{$lang->genericproducts}</div>
                        <div class="thead" style="display:table-cell;"></div>
                    </div>
                    <div style="display:table-row; vertical-align:top;">
                        <div style="display: table-cell; padding:5px;">{$lang->selectgenericproducts}</div>
                        <div style="display: table-cell; padding:5px;">
                            <table>
                                <tbody id="genericproducts_tbody" >
                                    {$genericproducts_rows}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div style="display:table-row; margin-bottom: 10px;">
                        <div style="display: table-cell; padding:5px;"> <img src="images/add.gif" id="addmore_genericproducts" alt="{$lang->add}" />
                            <input name="genericproduct_numrows" id="genericproduct_numrows" value="{$genericproduct_rowid}" type="hidden" />
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div class="thead" style="display:table-cell;">{$lang->representative}</div>
                        <div class="thead" style="display:table-cell;"></div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell; padding:5px; vertical-align:top;">{$lang->selectcontactperson}</div>
                        <div style="display: table-cell; padding:5px;">
                            <table>
                                <tbody id="representative_tbody">
                                    {$contactpersons_rows}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div style="display:table-row; margin-bottom: 10px;">
                        <div style="display: table-cell; padding:5px;"> <img src="images/add.gif" id="addmore_representative" alt="{$lang->add}" />
                            <input name="representative_numrows" id="representative_numrows" value="{$contactp_rowid}" type="hidden" />
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div class="thead" style="display:table-cell;">{$lang->feedback}</div>
                        <div class="thead" style="display:table-cell;"></div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell; padding:5px;">{$lang->maturitylevel}</div>
                        <div style="display: table-cell;">{$rml_selectlist}</div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell; vertical-align:middle;">{$lang->commentstoshare}</div>
                        <div style="display: table-cell; padding:5px; vertical-align:top;">
                            <textarea tabindex="25" class="basictxteditadv" cols="35" rows="5" name="supplier[commentsToShare]" id="commentsToShare">{$supplier[details][commentsToShare]}</textarea>
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell; vertical-align:middle;">{$lang->marketingrecords}</div>
                        <div style="display: table-cell; padding:5px; vertical-align:top;">
                            <textarea  tabindex="26" class="basictxteditadv" cols="35" rows="5" name="supplier[marketingRecords]" id='marketingRecords'>{$supplier[details][marketingRecords]}</textarea>
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell; vertical-align:middle;">{$lang->cobriefing}</div>
                        <div style="display: table-cell; padding:5px; vertical-align:top;">
                            <textarea tabindex="27" class="basictxteditadv" cols="35" rows="5" name="supplier[coBriefing]" id='coBriefing'>{$supplier[details][coBriefing]}</textarea>
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell; vertical-align:middle;">{$lang->historical}</div>
                        <div style="display: table-cell; padding:5px; vertical-align:top;">
                            <textarea tabindex="28" class="basictxteditadv" cols="35" rows="5" name="supplier[historical]" id='historical'>{$supplier[details][historical]}</textarea>
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell; vertical-align:middle;">{$lang->sourcingrecords}</div>
                        <div style="display: table-cell; padding:5px; vertical-align:middle;">
                            <textarea tabindex="29" class="basictxteditadv" cols="35" rows="5" name="supplier[sourcingRecords]" id="sourcingRecords">{$supplier[details][sourcingRecords]}</textarea>
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell; vertical-align:middle;">{$lang->productfunction}</div>
                        <div style="display: table-cell; padding:5px; vertical-align:middle;">
                            <textarea tabindex="30" class="basictxteditadv" cols="35" rows="5" name="supplier[productFunction]" id='productFunction'>{$supplier[details][productFunction]}</textarea>
                        </div>
                    </div>
                    {$blacklist_button}
                    <div style="display:table-row;">{$mark_blacklist}</div>
                    <div  style="margin-bottom: 0.9em;"></div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">
                            <input type="button" class="button main" value="{$lang->savecaps}" id="perform_sourcing/managesupplier_Button" />
                        </div>
                        <div style="display: table-cell;">
                            <input type="reset" class="button" value="{$lang->reset}"/>
                        </div>
                    </div>
                </div>
                <div style="display:table-row">
                    <div style="display:table-cell;"id="perform_sourcing/managesupplier_Results"></div>
                </div>
            </form>
            {$popup_sourcingblhistory}
        </td>
    </tr>
    {$footer}
</body>
</html>