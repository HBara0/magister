<div id="popup_weekview_createentry"  title="{$lang->createvisitorreport}">
    <form name='perform_calendar/weekviewoperations_Form' id="perform_calendar/weekviewoperations_Form" method="post">
        <input type="hidden" id="uid" name="uid" value="{$core->user[uid]}" />
        <table>
            <tr>
                <td style="width: 30%; font-weight:bold;">{$lang->fromdate}</td>
                <td>
                    <input type="text" id="pickDate_from" name="pickDate_from" autocomplete="off" tabindex="1" value="" required="required" />
                    <input type="hidden" name="visit[fromDate]" id="altpickDate_from"  />
                    <input name="fromHour" type="text" id="fromHour" size="2" required="required"> <input name="fromMinutes" type="text" id="fromMinutes" size="2"  required="required">
                    <span style="font-style:italic;" class="smalltext">(hh mm)</span>
                </td>
            </tr>
            <tr>
                <td style="font-weight:bold;">{$lang->todate}</td>
                <td>
                    <input type="text" id="pickDate_to" name="pickDate_to" autocomplete="off" tabindex="2"  required="required"/>
                    <input type="hidden" name="visit[toDate]" id="altpickDate_to" />
                    <input name="toHour" type="text" id="toHour" size="2" required="required">
                    <input name="toMinutes" type="text" id="toMinutes" size="2" required="required">
                    <span style="font-style:italic;" class="smalltext">(hh mm)</span>
                </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">{$lang->customername}</td>
                <td>
                    <input type='text' id='customer_1_QSearch' value="" autocomplete="off" required="required" />
                    <a href="index.php?module=contents/addentities&amp;type=customer" target="_blank"><img src="./images/addnew.png" border="0" alt="{$lang->add}" title="{$lang->add}"></a>
                    <input type='hidden'  id='customer_1_id' name='cid' value=""/>
                    <div id='searchQuickResults_customer_1' class='searchQuickResults' style='display:none;'></div>
                </td>
            </tr>
            <tr>
                <td style="font-weight:bold;">{$lang->calltype}</td>
                <td>
                    <select name="type" id="type" required="required">
                        <option value=""></option>
                        <option value="1">{$lang->facetoface}</option>
                        <option value="2">{$lang->telephonecall}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="font-weight:bold;">{$lang->callpurpose}</td>
                <td>
                    <select name="purpose" id="purpose" required="required">
                        <option value=""></option>
                        <option value="1">{$lang->followup}</option>
                        <option value="2">{$lang->service}</option>
                        <option value="3">{$lang->prospective}</option>
                    </select>
                </td>
            </tr>
        </table>  
        <hr />
        <input type='submit' class='button' value='{$lang->create}' id='perform_calendar/weekviewoperations_Button' />
    </form>
    <div id="perform_calendar/weekviewoperations_Results" ></div>
    <div id="suggestions_Results" class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-top: 10px; margin-bottom: 10px; display: none;"></div>
</div>