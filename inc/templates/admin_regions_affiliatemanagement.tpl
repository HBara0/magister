<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->affiliatemanagement}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <div class="container">
                <div>
                    <h3>{$lang->affiliatemanagement}</h3>
                </div>
                <div class='row'>
                    <div class='col-sm-12 col-xs-12 col-md-9 bkgcolor_greygradient'>
                        <form name="perform_regions/affiliatemanagement_Form" id="perform_regions/affiliatemanagement_Form" action="#" method="post" class="contactform">
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3">{$lang->generalmanager}</div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <input type="hidden" name="affid" value="{$affid}"/>
                                    <input class="form-control" type='text' id='user_1_autocomplete' value="{$affiliate[generalManager_output]}"/>
                                    <input type='hidden' id='user_1_id' name='generalManager' value="{$affiliate[generalManager]}" />
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3">
                                    <input class="form-control" style="width:35%;" type="text" size="3" id="user_1_id_output" value="{$affiliate[generalManager]}" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3">{$lang->supervisor}</div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <input class="form-control" type='text' id='user_2_autocomplete' value="{$affiliate[supervisor_output]}"/>
                                    <input type='hidden' id='user_2_id' name='supervisor' value="{$affiliate[supervisor]}" />
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3">
                                    <input class="form-control" style="width:35%;" type="text" size="3" id="user_2_id_output" value="{$affiliate[supervisor]}" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3">{$lang->hrmanager}</div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <input class="form-control" type='text' id='user_3_autocomplete' value="{$affiliate[hrManager_output]}"/>
                                    <input type='hidden' id='user_3_id' name='hrManager' value="{$affiliate[hrManager]}" />
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3">
                                    <input class="form-control" style="width:35%;" type="text" size="3" id="user_3_id_output" value="{$affiliate[hrManager]}" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3">{$lang->finmanager}</div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <input class="form-control" type='text' id='user_4_autocomplete' value="{$affiliate[finManager_output]}"/>
                                    <input type='hidden' id='user_4_id' name='finManager' value="{$affiliate[finManager]}" />
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3">
                                    <input class="form-control" style="width:35%;" type="text" size="3" id="user_4_id_output" value="{$affiliate[finManager]}" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3">{$lang->coo}</div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <input class="form-control" type='text' id='user_5_autocomplete' value="{$affiliate[coo_output]}"/>
                                    <input type='hidden' id='user_5_id' name='coo' value="{$affiliate[coo]}" />
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3">
                                    <input class="form-control" style="width:35%;" type="text" size="3" id="user_5_id_output" value="{$affiliate[coo]}" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3">{$lang->regionalsupervisor}</div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <input class="form-control" type='text' id='user_6_autocomplete' value="{$affiliate[regionalSupervisor_output]}"/>
                                    <input type='hidden' id='user_6_id' name='regionalSupervisor' value="{$affiliate[regionalSupervisor]}" />
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3">
                                    <input class="form-control" style="width:35%;" type="text" size="3" id="user_6_id_output" value="{$affiliate[regionalSupervisor]}" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3">{$lang->globalpurchasemanager}</div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <input class="form-control" type='text' id='user_7_autocomplete' value="{$affiliate[globalPurchaseManager_output]}"/>
                                    <input type='hidden' id='user_7_id' name='globalPurchaseManager' value="{$affiliate[globalPurchaseManager]}" />
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3">
                                    <input class="form-control" style="width:35%;" type="text" size="3" id="user_7_id_output" value="{$affiliate[globalPurchaseManager]}" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3">{$lang->cfo}</div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <input class="form-control" type='text' id='user_8_autocomplete' value="{$affiliate[cfo_output]}"/>
                                    <input type='hidden' id='user_8_id' name='cfo' value="{$affiliate[cfo]}" />
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3">
                                    <input class="form-control" style="width:35%;" type="text" size="3" id="user_8_id_output" value="{$affiliate[cfo]}" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3">{$lang->logisticsmanager}</div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <input class="form-control" type='text' id='user_9_autocomplete' value="{$affiliate[logisticsManager_output]}"/>
                                    <input type='hidden' id='user_9_id' name='logisticsManager' value="{$affiliate[logisticsManager]}" />
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3">
                                    <input class="form-control" style="width:35%;" type="text" size="3" id="user_9_id_output" value="{$affiliate[logisticsManager]}" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3">{$lang->commercialmanager}</div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <input class="form-control" type='text' id='user_10_autocomplete' value="{$affiliate[commercialManager_output]}"/>
                                    <input type='hidden' id='user_10_id' name='commercialManager' value="{$affiliate[commercialManager]}" />
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3">
                                    <input class="form-control" style="width:35%;" type="text" size="3" id="user_10_id_output" value="{$affiliate[commercialManager]}" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3">{$lang->globalfinmanager}</div>
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <input class="form-control" type='text' id='user_11_autocomplete' value="{$affiliate[globalFinManager_output]}"/>
                                    <input type='hidden' id='user_11_id' name='globalFinManager' value="{$affiliate[globalFinManager]}" />
                                </div>
                                <div class="col-xs-12 col-sm-3 col-md-3">
                                    <input class="form-control" style="width:35%;" type="text" size="3" id="user_11_id_output" value="{$affiliate[globalFinManager]}" disabled/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-6">
                                    <input type="button" value="save" id="perform_regions/affiliatemanagement_Button" class="btn btn-success"/>
                                    <input class="btn btn-success" type="reset" value="{$lang->reset}"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-6" id="perform_regions/affiliatemanagement_Results"></div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </td>
    </tr>
    {$footer}
</html>