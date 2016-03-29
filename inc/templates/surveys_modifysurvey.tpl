<h1>{$lang->modifysurvey}</h1>
<form  name="perform_surveys/managesurveys_Form" id="perform_surveys/managesurveys_Form" action="#" method="post">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="20%">{$lang->reference}</td>
            <td width="80%"><input type="text" name="reference" id="reference"  tabindex="1" value="{$survey[reference]}" />

                <input type="hidden" name="sid" id="sid"  tabindex="1" value="{$survey[sid]}" /></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">{$lang->subject}</td>
            <td><input type="text" name="subject" id="subject"  tabindex="1" value="{$survey[subject]}" size="60"/></td>
        </tr>
        <tr>
            <td>{$lang->description}</td>
            <td> <textarea  cols="50" rows="5"  name="description" >{$survey[description]}</textarea></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">{$lang->category}</td>
            <td>{$surveycategories_list}</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">{$lang->publicresult}</td>
            <td>{$radiobuttons[isPublicResults]}</td>
        </tr>
        <tr>
            <td>{$lang->closingdate}</td>
            <td><input type="text" id="pickDate" autocomplete="off" tabindex="1" value="{$survey[closingDate_output]}" title="{$lang->closingdate_tip}"/><input type="hidden" name="closingDate" id="altpickDate" value="{$survey[closingDate_value]}" /></td>
        </tr>
        <tr>
            <td colspan="2" class="thead">{$lang->surveysassociations} <a href="#associationssection" onClick="$('#associationssection').fadeToggle();">...</a></td>
        </tr>
        <tr>
            <td colspan="2" id="associationssection" >
                <table class="datatable" border="0" cellspacing="1" cellpadding="1">
                    <tr >
                        <td>{$lang->employee}</td>
                        <td>{$employees_list}</td>
                        <td>{$lang->supplier}</td>
                        <td>
                            <input type='text'    id='supplier_1_autocomplete'  value="{$survey[associations][suppliername]}" autocomplete='off' size='40px'/>
                            <input type='hidden' id='supplier_1_id' name='associations[spid]' value="{$survey[associations][spid]}" />
                            <div id='searchQuickResults_supplier_1' class='searchQuickResults' style='display:none;'></div>
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->affiliate}</td>
                        <td>{$affiliates_list}</td>
                        <td >{$lang->product}</td>
                        <td>
                            <input type='text' id='product_1_autocomplete' value="{$survey[associations][productname]}" autocomplete='off' size='40px'/>
                            <input type='hidden' id='product_1_id' name='associations[pid]' value="{$survey[associations][pid]}" />
                            <div id='searchQuickResults_product_1' class='searchQuickResults' style='display:none;'></div>
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->segment}</td>
                        <td>{$segments_list}</td>
                        <td>{$lang->other}</td>
                        <td><input name="associations[other]" type="text" value="{$survey[associations][other]}"></td>
                    </tr>
                    <tr>
                        <td>{$lang->country}</td>
                        <td>{$countries_list}</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <hr />
                <input type="button" value="{$lang->savecaps}" id="perform_surveys/managesurveys_Button" tabindex="26" class="button"/> <input type="reset" value="{$lang->reset}" class="button" />
                <div id="perform_surveys/managesurveys_Results"></div>
            </td>
        </tr>
    </table>
</form>
