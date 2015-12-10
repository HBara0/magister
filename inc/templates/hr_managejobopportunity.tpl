<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->managejobopportunities}</title>
        {$headerinc}
        <script>
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->managejobopportunities}</h1>
            <form action="#" method="post" id="perform_hr/managejobopportunity_Form" name="perform_hr/managejobopportuity_Form">
                <table width="100%" border="0" cellpadding="0" cellspacing="1" class="datatable">
                    <tr>
                        <td colspan="2"><h2>{$lang->offerdetails}</h2></td>
                    </tr>
                    <tr>
                        <td>{$lang->reference}</td>
                        <td><input type="text" id="" name="jobopportunity[reference]" value="{$jobopportunity[reference]}" required="required" style="width:200px;"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->affiliate}*</td>
                        <td>{$affiliates_list}</td>
                    </tr>
                    <tr class="thead">
                        <td colspan="2">{$lang->employmentdetails}</td>
                    </tr>
                    <tr>
                        <td>{$lang->worklocation}*</td>
                        <td>
                            <input type="text" autocomplete="off" id="cities_cache_0_autocomplete" value="{$jobopportunity[workLocation_output]}" required="required" style="width:200px;"/>
                            <input type='hidden' id='cities_cache_0_id'   name="jobopportunity[workLocation]" value="{$jobopportunity[workLocation]}"/>
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->title}*</td>
                        <td>
                            <input type="text" id="jobopportunity_title" name="jobopportunity[title]" value="{$jobopportunity[title]}" style="width:200px;"/>
                        </td>
                    </tr>

                    <tr>
                        <td>{$lang->employementtype}*</td>
                        <td>{$employmenttype_list}</td>
                    </tr>
                    <tr>
                        <td>{$lang->shortdescription}*</td>
                        <td>
                            <textarea name="jobopportunity[shortDesc]" cols="40" rows="6" id="jobopportunity_shortDesc" class="txteditadv">{$jobopportunity[shortDesc]}</textarea></td>
                    </tr>
                    <tr>
                        <td>{$lang->responsibilities}*</td>
                        <td colspan="2">
                            <textarea name="jobopportunity[responsibilities]" cols="40" rows="6" id="jobopportunity_responsibilities" class="txteditadv">{$jobopportunity[responsibilities]}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->manageothers}</td>
                        <td>{$radiobuttons[managesOthers]}</td>
                    </tr>

                    <tr>
                        <td>{$lang->salary}</td>
                        <td><input type="text" id="jobopportunity_salary" name="jobopportunity[salary]" value="{$jobopportunity[salary]}" style="width:200px;"/>{$currencies_list}
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->approximatejoindate}</td>
                        <td>
                            <input type="text" id="pickDate_1_from" autocomplete="off" value="{$jobopportunity[approxJoinDate_output]}" style="width:200px;"/>
                            <input type="hidden" name="jobopportunity[approxJoinDate]" id="altpickDate_1_from" value="{$jobopportunity[approxJoinDate]}" />
                        </td>
                    </tr>

                    <tr class="thead">
                        <td colspan="2">{$lang->applicantrequirements}</td>
                    </tr>
                    <tr>
                        <td>{$lang->gender}</td>
                        <td>
                            <input type="checkbox" name="jobopportunity[gender]" {$checked[gender][male]} value=""/> {$lang->male}
                            <input type="checkbox" name="jobopportunity[gender]" {$checked[gender][female]}/> {$lang->female}
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->nationality}</td>
                        <td>{$nationality_list}</td>
                    </tr>
                    <tr>
                        <td>{$lang->residence}</td>
                        <td>
                            <input type="text" autocomplete="off" id="cities_cache_1_autocomplete" value="{$jobopportunity[residence_output]}" style="width:200px;"/>
                            <input type='hidden' id='cities_cache_1_id' name="jobopportunity[residence]" value="{$jobopportunity[residence]}"/>
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->drivinglicenserequired}</td>
                        <td>{$radiobuttons[drivingLicReq]}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;">{$lang->requiredlanguages}</td>
                        <td style="vertical-align:top;">{$languages_list} </td>
                    </tr>
                    <tr>
                        <td>{$lang->careerlevel}</td>
                        <td>{$careerlevel_list}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;">
                            {$lang->minimumedutcationlevel}</td>
                        <td style="vertical-align:top;">
                            <!--  <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                  <table class="datatable" width="100%">
                                      <thead>
                                          <tr>
                                              <th width="100%"><input type="checkbox" id='educationlevelfilter_checkall'><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->educationlevel}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
                                          </tr>
                                      </thead>
                                      <tbody >
                            {$educationLevel_list}
                        </tbody>
                    </table>
                </div>
                            -->
                            {$educationlevel_list}
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->yearsofexperience}</td>
                        <td>{$lang->min} <input type="number" id="jobopportunity_minExpYears" name="jobopportunity[minExpYears]" value="{$jobopportunity[minExpYears]}" style="width:125px;"/>
                            {$lang->max} <input type="number" id="jobopportunity_maxExpYears" name="jobopportunity[maxExpYears]" value="{$jobopportunity[maxExpYears]}" style="width:125px;"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->minimumqualifications}</td>
                        <td> <textarea name="jobopportunity[minQualifications]" id="jobopportunity_minQualifications" cols="40" row="5" class="txteditadv">{$jobopportunity[minQualifications]}</textarea>
                    </tr>

                    <tr>
                        <td>{$lang->preferredqualifications}</td>
                        <td> <textarea name="jobopportunity[prefQualifications]" id="jobopportunity_prefQualifications" cols="40" rows="5" class="txteditadv">{$jobopportunity[prefQualifications]}</textarea>
                    </tr>
                    <tr class="thead">
                        <td colspan="2">{$lang->publishoptions}</td>
                    </tr>
                    <tr>
                        <td>{$lang->publishon}*</td>
                        <td>
                            <input type="text" id="pickDate_2_from" autocomplete="off" value="{$jobopportunity[publishOn_output]}" style="width:200px;"/>
                            <input type="hidden" name="jobopportunity[publishOn]" id="altpickDate_2_from" value="{$jobopportunity[publishOn]}" />
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->unpublishon}*</td>
                        <td>
                            <input type="text" id="pickDate_3_from" autocomplete="off" value="{$jobopportunity[unpublishOn_output]}" style="width:200px;"/>
                            <input type="hidden" name="jobopportunity[unpublishOn]" id="altpickDate_3_from" value="{$jobopportunity[unpublishOn]}" />
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->publishingtimezone}</td>
                        <td>{$time_zones}</td>
                    </tr>
                    <tr>
                        <td>{$lang->allowsocialsharing}</td>
                        <td>{$radiobuttons[allowSocialSharing]}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><br/><h2>{$lang->autofilterconfig}</h2></td>
                    </tr>
                    <tr>
                        <td>{$lang->filtertype}</td>
                        <td>{$filter[types]}</td>
                    </tr>
                    <tr>
                        <td>{$lang->yearsofexperience}</td>
                        <td>{$lang->min} <input type="number" id="filter_minExpYears" name="filter[minExpYears]" value="{$filter[minExpYears]}" style="width:125px;"/>
                            {$lang->max} <input type="number" id="filter_minExpYears" name="filter[minExpYears]" value="{$filter[maxExpYears]}" style="width:125px;"/></td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;">{$lang->minimumedutcationlevel}</td>
                        <!-- <td>
                               <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                   <table class="datatable" width="100%">
                                       <thead>
                                           <tr>
                                               <th width="100%"><input type="checkbox" id='educationlevelfilter_checkall'><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->educationlevel}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
                                           </tr>
                                       </thead>
                                       <tbody >
                        {$filter[educationLevel_list]}
                    </tbody>
                </table>
            </div>-->
                        <td style="vertical-align:top;">
                            {$filter[educationlevel_list]}
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->gender}</td>
                        <td>{$filter[gender]}</td>
                    </tr>
                    <tr>
                        <td>{$lang->age}</td>
                        <td>{$lang->min} <input type="number" id="filter_salary" name="filter[minExpYears]" value="{$filter[minExpYears]}" style="width:125px;"/>
                            {$lang->max} <input type="number" id="filter_salary" name="filter[maxExpYears]" value="{$filter[maxExpYears]}" style="width:125px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->careerlevel}</td>
                        <td>{$careerlevel_list}</td>
                    </tr>
                    <tr>
                        <td>{$lang->residence}</td>
                        <td>
                            <input type="text" autocomplete="off" id="cities_cache_2_autocomplete" value="{$filter[residence_output]}" style="width:200px;"/>
                            <input type='hidden' id='cities_cache_2_id'   name="filter[residence]" value="{$filter[residence]}"/>
                        </td>
                    </tr>
                    <tr>
                        <td>{$lang->experienceindustry}</td>
                        <td>{$filter[experienceindustry_list]}</td>
                    </tr>
                    <tr class="thead">
                        <td colspan="2">{$lang->onlineinterview}</td>
                    </tr>
                    <tr>
                        <td>{$lang->residence}</td>
                        <td>{$filter[residence_list]}</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" value="{$lang->savecaps}" id="perform_hr/managejobopportunity_Button" class="button"/> <input type="reset" value="{$lang->reset}" class="button"/>
                            <div id="perform_hr/managejobopportunity_Results"></div>
                        </td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>