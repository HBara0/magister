{$maintenancenotice}
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="hidden-print">
        <td width="12%" class="magisterLogo"><a href="{$core->settings[rootdir]}"><img src="{$core->settings[rootdir]}/images/magister_logo.jpg" border="0" alt='{$core->settings[systemtitle]}' /></a></td>
        <td width="78%" class="topContent"><div id="welcome_message">{$lang->welcomeuser}&nbsp;<a href="{$settings[rootdir]}/users.php?action=profile&amp;do=edit"><img src="{$core->settings[rootdir]}/images/editprofile.gif" border='0' alt="{$lang->edityouraccount}"/></a>
                {$mainpageslink}{$admincplink}|
                <a href='{$settings[rootdir]}/users.php?action=do_logout'>{$lang->logout}</a>
                <br />{$lang->lastvisit}</div></td>
        <td width="10%">&nbsp;</td>
    </tr>