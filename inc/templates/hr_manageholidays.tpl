<script language="javascript" type="text/javascript">
    $(function() {
        $("#affid").change(function() {
            if(sharedFunctions.checkSession() == false) {
                return;
            }

            sharedFunctions.requestAjax("post", "index.php?module=hr/manageholidays&action=get_affiliateemployees", "affid=" + $(this).val(), 'exceptionsemployees_list', 'exceptionsemployees_list', true);
        });

        $("input[id='isOnce']").change(function() {
            if($(this).is(":checked")) {
                $("#year").removeAttr("disabled");
            }
            else
            {
                $("#year").attr("disabled", "true");
            }
        });
    });
</script>
<h1>{$pagetitle}</h1>
<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"><p><strong>Important:</strong> Holidays recurring on the same date should not be created each year. Holidays which dates change should be re-created each year.</p></div>

<form name='change_hr/manageholidays_Form' id="change_hr/manageholidays_Form" method="post"  >
    <input type="hidden" id="action" name="action" value="{$action}" />
    {$affid_field}
    {$hid_field}
    <table width="100%" class="datatable">
        <thead>
            <tr>
                <th style="width:30%;">{$lang->name}</th>
                <th style="width:18%;">{$lang->month}</th>
                <th style="width:18%;">{$lang->day}</th>
                <th style="width:17%;">{$lang->year}</th>
                <th style="width:17%;">{$lang->days}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center"><input type='text' name="title" id="title" required="required" value="{$holiday[title]}"/> <input type='checkbox' name='isOnce' id='isOnce' value="1"{$checkedboxes[isOnce]}>{$lang->once}</td>
                <td align="center">{$months_list}</td>
                <td align="center">{$days_list}</td>
                <td align="center"><input type='text' name="year" id="year" maxlength="4" size="4" accept="numeric" value="{$holiday[year]}"{$year_disabled}/></td>
                <td align="center"><input type='text' name="numDays" id="numDays" size="4" maxlength="4" required="required"  value="{$holiday[numDays]}" accept="numeric"/></td>
            </tr>
            <tr>
                <td colspan="5"><hr /><div class="subtitle">{$lang->validityperiod}</div><span class="smalltext">{$lang->validityperiod_notes}</span></td>
            </tr>
            <tr>
                <td colspan="5">
                    <div style='display:inline-block; width:15%;'>{$lang->fromdate}</div>
                    <div style='display:inline-block; width:75%;'>
                        <input type="text" id="pickDate_holidayfromdate" name="validFrom" autocomplete="off" ttabindexabindex="2" value="{$holiday[validFromOuptut]}"  />
                        <input type="time" name="fromTime" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})" title="{$lang->hoursfrom}" value="{$holiday[fromTime]}" placeholder="00:00">
                    </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <div style='display:inline-block; width:15%;'>{$lang->todate}</div>
                    <div style='display:inline-block; width:75%;'>
                        <input type="text" id="pickDate_holidaytodate" name="validTo" autocomplete="off" tabindex="2"value="{$holiday[validToOutput]}" />
                        <input type="time" name="toTime" pattern="(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})"  title="{$lang->hoursto}" value="{$holiday[toTime]}" placeholder="23:59" >
                    </div>
                </td>
            </tr>
        </tbody>
        <tr>
            <td colspan="5">
                <hr />
                <span class="subtitle">{$lang->exceptfollowingemployees}</span>
                <div id="exceptionsemployees_list">{$exceptionsemployees_list}</div>
            </td>
        </tr>
        <tr>
            <td colspan="8"><hr /><input type='submit' class='button' value='{$lang->savecaps}' id='change_hr/manageholidays_Button' /> </td>
        </tr>
    </table>
</form>
<div id="change_hr/manageholidays_Results"></div>
