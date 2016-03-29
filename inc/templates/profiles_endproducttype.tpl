<link href="{$core->settings[rootdir]}/css/rateit.min.css" rel="stylesheet" type="text/css">
<link href="{$core->settings[rootdir]}/css/rml.min.css" rel="stylesheet" type="text/css">
<style type="text/css">
    span.listitem:hover { border-bottom: #CCCCCC solid thin; }
</style>
<script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        $("a[id^='loadentityusers_'],a[id^='loadallusers_']").click(function() {
            if(sharedFunctions.checkSession() == false) {
                return;
            }
            var ids = $(this).attr("id").split('_');

            if(ids[0] == 'loadentityusers') {
                var action = 'getentityusers';
            }
            else
            {
                var action = 'getallusers';
            }

            sharedFunctions.requestAjax("post", "index.php?module=profiles/entityprofile&action=" + action, "affid=" + ids[1] + '&eid=' + ids[2], 'entityusers', 'entityusers', true);
        });
    {$header_rmljs}
    {$header_ratingjs}
    });
</script>
<script src="{$core->settings[rootdir]}/js/profiles_marketintelligence.js" type="text/javascript"></script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td colspan="2"><h1>{$profile[title]}</h1></td>
    </tr>
    <tr><td colspan="2"><h4>{$details}</h4></td></tr>
    <tr>
        <td>
            <h2>{$application}/{$segment}</h2>
        </td>
    </tr>
    <tr><td>{$productslist}</td></tr>
    <tr><td>{$chemsubstanceslist}</td></tr>
    <tr><td>{$basicingredientlist}</td></tr>
    <tr><td>{$relatedbrandslist}</td></tr>
</table>
{$popup_createbrand}
{$popup_marketdata}

