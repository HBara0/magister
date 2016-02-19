<h1>{$lang->facilitytypesmgmt}</h1>
<form name="perform_facilitymgmt/managefacilitytype_Form" id="perform_facilitymgmt/managefacilitytype_Form"  action="#" method="post">
    <input type="hidden" id="" name="facility[fmftid]" value="{$facilitytype[fmftid]}">
    <table class="datatable" width="100%" border="0" cellspacing="0" cellpadding="2">
        <tbody>
            <tr><td>{$lang->name}</td> <td><input type="text" name="type[title]" value="{$facilitytype[title]}"/></td></tr>
            <tr><td>{$lang->typecategory}</td><td>{$typeselectlist}</td></tr>
            <tr><td>{$lang->isactive}</td><td>{$isactive}</td></tr>
            <tr><td>{$lang->description}</td><td><textarea name='type[description]'>{$facilitytype[description]}</textarea></td></tr>

        </tbody>
    </table>
    <br/>
    <input type="submit" id="perform_facilitymgmt/managefacilitytype_Button" value="Save" class="button"/>
</form>
<div id="perform_facilitymgmt/managefacilitytype_Results"></div>
