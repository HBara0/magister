<html>
<head>
<title>{$core->settings[systemtitle]} | {$lang->addentity}</title>
{$headerinc}
</head>
<body>
{$header}
<tr>
{$menu}
<td class="contentContainer">
<h3>{$lang->addentity}</h3>
	<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p>{$lang->companyname_title} {$lang->companynameabbr_title}</p></div>
	<form action="#" method="post" id="perform_contents/addentities_Form" name="perform_contents/addentities_Form">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
         <tr>
            <td width="20%"><strong>{$lang->entitytype}</strong></td><td colspan="2">{$types_list}</td>
        </tr>
        <tr>
            <td><strong>{$lang->companyname}</strong></td><td width="30%"><input type="text" id="companyName" name="companyName" class="inlineCheck" title="{$lang->companyname_title}"/> {$lang->abbreviation} <input type="text" id="companyNameAbbr" name="companyNameAbbr" size='5' title="{$lang->companynameabbr_title}"/></td>
            <td width="50%" rowspan="3" valign="top"><span id="companyName_inlineCheckResult"></span></td>
        </tr>
        <tr>
            <td><strong>{$lang->segments}</strong></td><td>{$segments_list}</td>
        </tr>
        <tr>
            <td><strong>{$lang->affiliate}</strong></td><td>{$affiliates_list}</td>
          </tr>
        <tr>
            <td><strong>{$lang->country}</strong></td><td colspan="2">{$countries_list}</td>
        </tr>
        <tr>
            <td>{$lang->city}</td>
            <td colspan="2"><input type="text" id="city" name="city" /></td>
        </tr>
         <tr>
            <td>{$lang->address}</td>
            <td colspan="2"><input type="text" id="addressLine1" name="addressLine1" /><br /><input type="text" id="addressLine2" name="addressLine2" /></td>
        </tr>
        <tr>
           <td>{$lang->buildingname}</td><td colspan="2"><input type="text" id="building" name="building" /> <input type="text" id="floor" name="floor" size='3' maxlength="3" /></td>
        </tr>
        <tr>
           <td>{$lang->postcode}</td><td colspan="2"><input type="text" id="postCode" name="postCode" accept="numeric" /></td>
        </tr>
        <tr>
        	<td>{$lang->geolocation}</td><td colspan="2"><input type="text" name="geoLocation" id="geoLocation" /> <span class="smalltext">({$lang->longlattidue})</span></td>
        </tr>
         <tr>
           <td>{$lang->telephone}</td>
           <td colspan="2">+ <input type="text" id="telephone_intcode" name="telephone_intcode" size="3" maxlength="3" accept="numeric" /> <input type="text" id="telephone_areacode" name="telephone_areacode" size='4' maxlength="4" accept="numeric" /> <input type="text" id="telephone_number" name="telephone_number" accept="numeric"  /><br />
           + <input type="text" id="telephone2_intcode" name="telephone2_intcode" size="3" maxlength="3" accept="numeric" /> <input type="text" id="telephone2_areacode" name="telephone2_areacode" size='4' maxlength="4" accept="numeric" /> <input type="text" id="telephone2_number" name="telephone2_number" accept="numeric" /> </td>
        </tr>
        <tr>
           <td>{$lang->fax}</td>
           <td colspan="2">+ <input type="text" id="fax_intcode" name="fax_intcode" size="3" maxlength="3" accept="numeric" /> <input type="text" id="fax_areacode" name="fax_areacode" size='4' maxlength="4" accept="numeric" /> <input type="text" id="fax_number" name="fax_number" accept="numeric" /><br />
           	+ <input type="text" id="fax2_intcode" name="fax2_intcode" size="3" maxlength="3" accept="numeric" /> <input type="text" id="fax2_areacode" name="fax2_areacode" size='4' maxlength="4" accept="numeric"  /> <input type="text" id="fax2_number" name="fax2_number" accept="numeric" />
           </td>
        </tr>
        <tr>
           <td>{$lang->pobox}</td>
           <td colspan="2"><input type="text" id="poBox" name="poBox" accept="numeric" /></td>
        </tr>
         <tr>
           <td>{$lang->email}</td>
           <td colspan="2"><input type="text" id="email" name="mainEmail" /> <span id="email_Validation"></span></td>
        </tr>
       <tr>
           <td>{$lang->website}</td>
           <td><input type="text" id="website" name="website" /></td>
        </tr>
         <tr><td colspan="3"><hr /></td><tr>
         <td colspan="3" class="subtitle">{$lang->representatives}</td>
         </tr>
         <tr>
         	<td colspan="3">
            <table width="100%">
            <tbody id="representatives_tbody">
           	  <tr id='1'><td><input type='text' id='representative_1_QSearch' autocomplete='off' size='40px'/><input type='hidden' id='representative_1_id' name='representative[1][rpid]'/><a href='#representative_1_id' id='addnew_contents/addentities_representative'><img src='images/addnew.png' border='0' alt='{$lang->add}'></a><div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div></tr>   
           </tbody>
             <tr><td colspan="2"><img src="images/add.gif" id="addmore_representatives" alt="{$lang->add}"><input type="hidden" name="rep_numrows" id="numrows" value="1"></td><tr>
         	</table>            </td>
            </tr>
           <tr><td colspan="3"><hr /></td>
        <tr>
            <td colspan="3" align="left"><input name="createReports" id="createReports" type="checkbox" value="1"{$createreports_disabled}> {$lang->alsocreatecurrentreports}</td>
         </tr>
          <tr><td colspan="3"><hr /></td>
        <tr>
            <td colspan="3" align="left">
            	<input type="button" value="{$lang->add}" id="perform_contents/addentities_Button" /> <input type="reset" value="{$lang->reset}"/>
            	<div id="perform_contents/addentities_Results"></div>            </td>
        </tr>
        </table>
    </form>
</td>
  </tr>
{$footer}
</body>
</html>