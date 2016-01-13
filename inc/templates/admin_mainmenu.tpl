<td class="menuContainer">
    <ul id="mainmenu">
        <li><span id="home/index"><a href='index.php?module=home/index'>{$lang->home}</a></span></li>
        <li><span id="config/settings"><a href='index.php?module=config/settings'>{$lang->systemsettings}</a></span></li>
        <li class="expandable"><span id="users">{$lang->manageusers}</span>
            <div id="users_children_container" style="display: none;">
                <ul id="users_children">
                    <li><span id="users/view"><a href='index.php?module=users/view'>{$lang->viewusers}</a></span></li>
                    <li><span id="users/add"><a href='index.php?module=users/add'>{$lang->addusers}</a></span></li>
                    <li><span id="users/copyassignments"><a href='index.php?module=users/copyassignments'>{$lang->copyassignments}</a></span></li>
                </ul>
            </div>
        </li>
        <li class="expandable"><span id="regions">{$lang->regionaldistribution}</span>
            <div id="regions_children_container" style="display: none;">
                <ul id="regions_children">
                    <li><span id="regions/affiliates"><a href='index.php?module=regions/affiliates'>{$lang->affiliates}</a></span></li>
                    <li><span id="regions/countries"><a href='index.php?module=regions/countries'>{$lang->countries}</a></span></li>
                </ul>
            </div>
        </li>
        <li class="expandable"><span id="entities">{$lang->manageentities}</span>
            <div id="entities_children_container" style="display: none;">
                <ul id="entities_children">
                    <li><span id="entities/viewsuppliers"><a href='index.php?module=entities/viewsuppliers'>{$lang->viewsuppliers}</a></span></li>
                    <li><span id="entities/viewcustomers"><a href='index.php?module=entities/viewcustomers'>{$lang->viewcustomers}</a></span></li>
                    <li><span id="entities/add"><a href='index.php?module=entities/add'>{$lang->addentities}</a></span></li>
                    <li><span id="entities/manageentitiesfiles"><a href='index.php?module=entities/manageentitiesfiles'>{$lang->managefiles}</a></span></li>
                    <li><span id="entities/managebrands"><a href='index.php?module=entities/managebrands'>{$lang->managebrands}</a></span></li>
                    <li><span id="entities/paymenttermslist"><a href='index.php?module=entities/paymenttermslist'>{$lang->managepaymentterms}</a></span></li>

                </ul>
            </div>
        </li>
        <li class="expandable"><span id="managesystem">{$lang->managesystem}</span>
            <div id="managesystem_children_container" style="display: none;">
                <ul id="managesystem_children">
                    <li><span id="managesystem/tableslist"><a href='index.php?module=managesystem/tableslist'>{$lang->tableslist}</a></span></li>
                    <li><span id="managesystem/windowslist"><a href='index.php?module=managesystem/windowslist'>{$lang->windowslist}</a></span></li>
                    <li><span id="managesystem/managewindows"><a href='index.php?module=managesystem/managewindows'>{$lang->managewindows}</a></span></li>
                    <li><span id="managesystem/referencelists"><a href='index.php?module=managesystem/referencelists'>{$lang->referencelists}</a></span></li>
                    <li><span id="managesystem/managereferencelist"><a href='index.php?module=managesystem/managereferencelist'>{$lang->managereferencelist}</a></span></li>
                </ul>
            </div>
        </li>
        <li><span id="integration/matchdata"><a href='index.php?module=integration/matchdata'>{$lang->integration}</a></span></li>
        <li><span id="languages/list"><a href='index.php?module=languages/list'>{$lang->languages}</a></span></li>
        <li class="expandable"><span id="maintenance">{$lang->maintenance}</span>
            <div id="maintenance_children_container" style="display: none;">
                <ul id="maintenance_children">
                    <li><span id="maintenance/overview"><a href='index.php?module=maintenance/overview'>{$lang->overview}</a></span></li>
                    <li><span id="maintenance/logs"><a href='index.php?module=maintenance/logs'>{$lang->readlogs}</a></span></li>
                    <li><span id="maintenance/backupdb"><a href='index.php?module=maintenance/backupdb'>{$lang->backupdb}</a></span></li>
                    <li><span id="maintenance/optimizedb"><a href='index.php?module=maintenance/optimizedb'>{$lang->optimizedb}</a></span></li>
                    <li><span id="maintenance/phpinfo"><a href='index.php?module=maintenance/phpinfo'>{$lang->phpinfo}</a></span></li>
                </ul>
            </div>
        </li>
    </ul>
</td>