<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->managesuppliers}</title>
{$headerinc}
</head>
<body>
{$header}
<tr>{$menu}
	<td class="contentContainer"><h3>{$lang->$actiontype}</h3>
		<form name="perform_sourcing/managesupplier_Form" action="{$_SERVER[QUERY_STRING]}" method="post" id="perform_sourcing/managesupplier_Form" >
			<input type="hidden" value="do_{$actiontype}page" name="action" id="action" />
			<input name="supplier[ssid]" type="hidden" value="{$supplierid}">
			<div style="display:table; border-collapse:collapse; width:100%;">
				<div style="display:table-row;">
					<div class="thead" style="display:table-cell; width:20%;">{$lang->generalinfo}</div>
					<div class="thead" style="display:table-cell; width:80%;"></div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell;"><strong>{$lang->companyname}</strong></div>
					<div style="display: table-cell; padding:5px;">
						<input id="companyName" name="supplier[companyName]" type="text" value="{$supplier[details][companyName]}" class='inlineCheck' size="30">
						&nbsp;{$lang->abbreviation}
						<input name="supplier[companyNameAbbr]" type="text" value="{$supplier[details][companyNameAbbr]}" size="15">
						<div id="companyName_inlineCheckResult" style="paddinng:5px;" class="red_text"></div>
					</div>
					
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell; vertical-align:middle;">{$lang->product}</div>
					<div style="display: table-cell; padding:5px;">
						<select name="supplier[type]">
							<option value="t"{$selecteditems[type][t]}>{$lang->trader}</option>
							<option value="p"{$selecteditems[type][p]}>{$lang->producer}</option>
						</select>
					</div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell; vertical-align:middle;">{$lang->product}</div>
					<div style="display: table-cell; padding:5px;">{$product_list}</div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell; vertical-align:middle;">{$lang->activityarea}</div>
					<div style="display: table-cell; padding:5px;">{$activityarea_list}</div>
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
					<div style="display: table-cell;">{$lang->postcode}</div>
					<div style="display: table-cell; padding:5px;">
						<input type="text" value="{$supplier[details][postCode]}" name="supplier[postCode]" />
					</div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell;">{$lang->pobox}</div>
					<div style="display: table-cell; padding:5px;">
						<input size="10"type="text"  value="{$supplier[details][poBox]}" name="supplier[poBox]" />
					</div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell;">{$lang->phone1}</div>
					<div style="display: table-cell; padding:5px;"> +
						<input type="text" id="phone1_intcode" name="supplier[phone1][intcode]" value="{$supplier[details][phone1][0]}" size="3" maxlength="3" accept="numeric" />
						<input type="text" id="phone1_areacode" name="supplier[phone1][areacode]" value="{$supplier[details][phone1][1]}" size='4' maxlength="4" accept="numeric" />
						<input type="text" id="phone1_number" name="supplier[phone1][number]" value="{$supplier[details][phone1][2]}" accept="numeric"  />
						<br />
					</div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell;">{$lang->phone2}</div>
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
						<input type="text" id="fax_intcode" name="supplier[fax][intcode]" size="3" maxlength="3" accept="numeric" />
						<input type="text" id="fax_areacode" name="supplier[fax][areacode]" size='4' maxlength="4" accept="numeric" />
						<input type="text" id="fax_number" name="supplier[fax][number]" value="{$supplier[details][fax]}" accept="numeric" />
						<br />
					</div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell;">{$lang->email}</div>
					<div style="display: table-cell; padding:5px; ">
						<input accept="email" type="text" value="{$supplier[details][mainEmail]}" name="supplier[mainEmail]" />
					</div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell;">{$lang->website}</div>
					<div style="display: table-cell; padding:5px; ">
						<input type="text" value="{$supplier[details][website]}" name="supplier[website]" />
					</div>
				</div>
				<div style="display:table-row;">
					<div class="thead" style="display:table-cell;">{$lang->chemicalproducts}</div>
					<div class="thead" style="display:table-cell;"></div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell; padding:5px;">{$lang->selectchemicalproduct}</div>
					<div style="display: table-cell; padding:5px;">
						<table>
							<tbody id="chemicalproduct_tbody" >
								{$chemicalproducts_rows}
							</tbody>
						</table>
					</div>
				</div>
				<div style="display:table-row; margin-bottom: 10px;">
					<div style="display: table-cell; padding:5px;"> <img src="images/add.gif" id="addmore_chemicalproduct" alt="{$lang->add}">
						<input name="chemicalproduct_numrows" id="chemicalproduct_numrows" value="1" type="hidden">
					</div>
				</div>
				<div style="display:table-row;">
					<div class="thead" style="display:table-cell;">{$lang->representative}</div>
					<div class="thead" style="display:table-cell;"></div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell; padding:5px;">{$lang->selectcontactperson}</div>
					<div style="display: table-cell; padding:5px;">
						<table>
							<tbody id="representative_tbody">
								{$contactpersons_rows}
							</tbody>
						</table>
					</div>
				</div>
				<div style="display:table-row; margin-bottom: 10px;">
					<div style="display: table-cell; padding:5px;"> <img src="images/add.gif" id="addmore_representative" alt="{$lang->add}">
						<input name="representative_numrows" id="representative_numrows" value="1" type="hidden">
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
						<textarea tabindex="25" class="texteditormin" cols="35" rows="5" name="supplier[commentsToShare]">{$supplier[details][commentsToShare]}</textarea>
					</div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell; vertical-align:middle;">{$lang->marketingrecords}</div>
					<div style="display: table-cell; padding:5px; vertical-align:top;">
						<textarea  tabindex="26" class="texteditormin"  cols="35" rows="5" name="supplier[marketingRecords]">{$supplier[details][marketingRecords]}</textarea>
					</div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell; vertical-align:middle;">{$lang->coBriefing}</div>
					<div style="display: table-cell; padding:5px; vertical-align:top;">
						<textarea tabindex="27" class="texteditormin" cols="35" rows="5" name="supplier[coBriefing]">{$supplier[details][coBriefing]}</textarea>
					</div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell; vertical-align:middle;">{$lang->historical}</div>
					<div style="display: table-cell; padding:5px; vertical-align:top;">
						<textarea tabindex="28" class="texteditormin" cols="35" rows="5" name="supplier[historical]">{$supplier[details][historical]}</textarea>
					</div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell; vertical-align:middle;">{$lang->sourcingRecords}</div>
					<div style="display: table-cell; padding:5px; vertical-align:middle;">
						<textarea tabindex="29" class="texteditormin" cols="35" rows="5" name="supplier[sourcingRecords]">{$supplier[details][sourcingRecords]}</textarea>
					</div>
				</div>
				<div style="display:table-row;">
					<div style="display: table-cell; vertical-align:middle;">{$lang->productFunction}</div>
					<div style="display: table-cell; padding:5px; vertical-align:middle;">
						<textarea tabindex="30" class="texteditormin" cols="35" rows="5"name="supplier[productFunction]">{$supplier[details][productFunction]}</textarea>
					</div>
				</div>
				<div style="display:table-row;">{$mark_blacklist} </div>
				<div  style="margin-bottom: 0.9em;"></div>
				<div style="display:table-row;">
					<div style="display: table-cell;width:10px;">
						<input type="button" class="button" value="{$lang->$actiontype}" id="perform_sourcing/managesupplier_Button" />
						<input type="reset" class="button" value="{$lang->reset}"/>
					</div>
				</div>
			</div>
			<div style="display:table-row">
				<div style="display:table-cell;"id="perform_sourcing/managesupplier_Results"></div>
			</div>
		</form></td>
</tr>
{$footer}
</body>
</html>