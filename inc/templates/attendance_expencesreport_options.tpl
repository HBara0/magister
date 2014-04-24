<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->expensesreport}</title>
        {$headerinc}
        <script>
            $(function() {
                $("#dimensionfrom, #dimensionto").sortable({
                    connectWith: ".sortable",
                    revert: true, //revert to their new positions using a smooth animation.
                    cursor: "move",
                    opacity: 0.6,
                    tolerance: "intersect", //overlaps the item being moved to the other item by 50%.
                    dropOnEmpty: true, //Prevent all items in a list from being dropped into a separate, empty list
                    placeholder: "ui-state-highlight",
                    receive: function(event, ui) {
                        $("#dimensionto li").css('background', '#92d050');
                        $('#dimensions').val($("#dimensionto").sortable('toArray'));
                    }
                });
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->expensesreport}</h3>
            <form action="index.php?module=attendance/generatexpensesreport&amp;action=preview&amp;identifier={$identifier}" method="post" id="attendance\/leavexpensesreport_Form" name="attendance\/leavexpensesreport_Form">
                <div class="subtitle">{$lang->filters}</div>

                {$notification_message}

                <div style="display:block; padding: 10px;">
                    <div style="display:inline-block; vertical-align:top; width: 20%;"> {$lang->fromdate}  </div>
                    <div style="display:inline-block; vertical-align:top; width: 20%;"> <input type="text" id="pickDate_from" autocomplete="off" tabindex="1" value="" required="required"/>
                        <input type="hidden" name="expencesreport[filter][fromDate]" id="altpickDate_from" value="" /></div>
                </div>
                <div style="display:block; padding: 10px;">
                    <div style="display:inline-block; vertical-align:top; width: 20%;"> {$lang->todate}  </div>
                    <div style="display:inline-block; vertical-align:top; width: 20%;">  <input type="text" id="pickDate_to" autocomplete="off" tabindex="2" value="{$leave[toDate_output]}" required="required" />
                        <input type="hidden" name="expencesreport[filter][toDate]" id="altpickDate_to" value=""/></div>
                </div>
                <div style="display:block; padding: 10px;">
                    <div style="display:inline-block; vertical-align:top; width: 20%;">{$lang->affiliate}</div>
                    <div style="display:inline-block; width: 20%; vertical-align:top; "> {$affiliates_list}</div>
                    <div style="display:inline-block; vertical-align:top; width: 20%;">{$lang->leavetype}</div>
                    <div style="display:inline-block; width: 20%; vertical-align:top;">{$leavetype_list}</div>
                </div>
                <div style="display:block; padding: 10px;">
                    <div style="display:inline-block; width: 20%; vertical-align:top;">{$lang->employee}</div>
                    <div style="display:inline-block; width: 20%; vertical-align:top;">{$employees_list}</div>
                    <div style="display:inline-block; width: 20%; vertical-align:top;">{$lang->leaveexptype}</div>
                    <div style="display:inline-block; width: 20%; vertical-align:top;">{$leave_expencestypes_list}</div>
                </div>
                <div class="subtitle" style="margin:10px;">{$lang->dimensions}<input type='text' id='dimensions' name="expencesreport[dimension][]" value='' style='display:none;'></div>
                <div style="display:block; text-align: center;">
                    <div style="display:inline-block; width:45%; vertical-align:top;">
                        {$lang->availabledimensions}<br />
                        <ul id="dimensionfrom" class="sortable">
                            {$dimension_item}
                        </ul>
                    </div>
                    <div style="display:inline-block; width:45%; vertical-align:top;">
                        {$lang->selecteddimensions}<br />
                        <ul id="dimensionto" class="sortable">
                        </ul>
                    </div>
                </div>
                <div style="display:block;">
                    <hr />
                    <div style="display:inline-block; padding: 8px; margin:8px;"><input value="{$lang->generate}"  class="button" type="submit" id="attendance\/leavexpensesreport_Button"/></div>
                </div>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>

