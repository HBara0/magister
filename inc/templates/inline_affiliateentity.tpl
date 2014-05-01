<hr />
<div style="color:#333333;">
    <strong>{$lang->affjoinentity}:</strong><br />
    <form action="#" id="perform_affiliateentity_contents/addentities_Form" name="perform_affiliateentity_contents/addentities_Form" method="post">
        <input type="hidden" name="action" value="do_affiliateentity">
        <input type="hidden" name="entity" id="entity" value="{$eid}">
        {$lang->affiliate} {$affiliates_list}
        <input type="button" id="perform_affiliateentity_contents/addentities_Button" class="button" value="{$lang->join}">
    </form>
    <div id="perform_affiliateentity_contents/addentities_Results"></div>
</div>