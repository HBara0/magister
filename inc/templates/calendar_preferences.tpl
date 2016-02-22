<h1>{$lang->calendarpreferences}</h1>
<form name="perform_calendar/preferences_Form" id="perform_calendar/preferences_Form" action="#" method="post">
    <div style="width:20%; display:inline-block; clear:left; float:left;">{$lang->defaultview} Default view</div>
    <div style="width:50%; display:inline-block; clear:right;""><select name="defaultView" id="defaultView">
            <option value="1"{$selected[defaultView][1]}>{$lang->month}</option>
            <option value="2"{$selected[defaultView][2]}>{$lang->week}</option>
        </select></div>
    <div style="width:20%; display:inline-block; clear:left; float:left;">{$lang->excludeholidays}</div> <div style="width:50%; display:inline-block; clear:right;"><input type="checkbox" value="1" name="excludeHolidays" id="excludeHolidays"{$checkboxes[excludeHolidays]} /></div>
    <div style="width:20%; display:inline-block; clear:left; float:left;">{$lang->excludeevents}</div> <div style="width:50%; display:inline-block; clear:right;"><input type="checkbox" value="1" name="excludeEvents" id="excludeEvents"{$checkboxes[excludeEvents]} /></div>
    <div style="width:20%; display:inline-block; clear:left; float:left;">{$lang->excludleaves}</div> <div style="width:50%; display:inline-block; clear:right;"><input type="checkbox" value="1" name="excludeLeaves" id="excludeLeaves"{$checkboxes[excludeLeaves]} title="If you this will happen"/></div>
    <input type="hidden" name="action" id="action" value="save_calendarpreferences" />

    <div align="center" style="width:100%; clear:both;">
        <hr />
        <div style="width:45%; height: 200px; overflow:auto; display:inline-block; vertical-align:top;">
            <table class="datatable" width="100%">
                <thead>
                    <tr><th class="thead" colspan='3'>{$lang->excludeemployees}</th></tr>
                </thead>
                {$relatedemployees_list}
            </table>
        </div>

        <div style="width:45%; display:inline-block; vertical-align:top; height: 200px; overflow:auto;">
            <table class="datatable" width="100%">
                <thead>
                    <tr><th class="thead" colspan='2'>{$lang->excludeaffiliates}</th></tr>
                </thead>
                {$relatedaffiliates_list}
            </table>
        </div>
    </div>
    <br />
    <hr />
    <input type="button" id="perform_calendar/preferences_Button" value="{$lang->savecaps}" class="button" />
</form>
<div id="perform_calendar/preferences_Results"></div>
