<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillmontlyreport}</title>
        {$headerinc}
        <style>
            #dimensionfrom, #dimensionto {
                list-style-type: none;
                margin: 10;
                padding: 0;
                float: left;
                margin-right: 10px;
                -webkit-border-radius: 5px 4px 7px 0px;
                -moz-border-radius:  1px 4px 7px 0px;
                border-radius: 1px 4px 7px 0px;
                border: 3px solid  #efefef;
                background:#ffffff;
                padding: 5px;
                width: 50%;
            }
            #dimensionfrom li, #dimensionto li  {
                margin:5px;
                padding: 5px;
                background: #efefef;
                font-size: 13PX;
                width: 70%;
            }
            #dimensionfrom li:hover, #dimensionto li:hover{
                cursor:move;
            }
            .sortable-placeholder {
                opacity: 0.6;
            }

        </style>

        <script>
            $(function() {
                $("#dimensionfrom, #dimensionto")
                $("#dimensionfrom, #dimensionto").sortable({
                    connectWith: ".connectedSortable",
                    revert: true, //revert to their new positions using a smooth animation.
                    cursor: "wait",
                    tolerance: "intersect", //overlaps the item being moved to the other item by 50%.
                    over: function() {
                        $('.sortable-placeholder').hide();
                    },
                    dropOnEmpty: true, //Prevent all items in a list from being dropped into a separate, empty list
                    start: function() {        /*  return back the Color of the  element to tis origin Upon remove of the item */
                        $("#dimensionto li").animate({
                            opacity: 2.35,
                            backgroundColor: "#efefef",
                        });
                    },
                    stop: function(event, ui) {
                        $("#dimensionto li").css('background', '#92d050');
                        $('#field').val($("#dimensionto").sortable('toArray'));
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

            <form action="index.php?module=attendance/generatexpensesreport&amp;action=preview&amp;referrer=generate&amp;identifier={$identifier}" method="post" id="attendance\/leavexpensesreport_Form" name="attendance\/leavexpensesreport_Form">
                <div class="thead" style="margin:10px;">{$lang->filters}</div>
                <div style="display:block; border:">
                    <div style="display:inline-block;vertical-align:top; padding:15px; margin:20px;">{$lang->affiliate}</div>
                    <div style="display:inline-block; margin:20px;"><select name="expencesreport[filter][affids][]" multiple="multiple">{$affiliates_list}</select></div>
                    <div style="display:inline-block; padding:15px; margin:20px;vertical-align:top;">{$lang->leavetype}</div>
                    <div style="display:inline-block;margin:20px;"> {$leavetype_list}</div>
                </div>


                <div style="display:block;padding:8px;">
                    <div style="display:inline-block; padding: 8px; margin:8px; margin:25px; vertical-align:top; ">{$lang->employee}</div>
                    <div style="display:inline-block;"> {$employees_list}</div>
                    <div style="display:inline-block; padding:15px; margin:20px;vertical-align:top;">{$lang->leaveexptype}</div>
                    <div style="display:inline-block;"> {$leave_expencestypes_list}</div>
                </div>

                <div class="thead" style="margin:10px;">{$lang->dimensions}</div>
                <div style="display:block; ">
                    <div style="display:inline-block;width:40%;  vertical-align:top;">
                        <ul id="dimensionfrom"   class="connectedSortable">
                            {$dimension_item}
                        </ul>
                    </div>
                    <div style="display:inline-block;width:40%; vertical-align:top;">
                        <ul id="dimensionto" class="connectedSortable">
                            <li class="sortable-placeholder" style="background:none;">{$lang->drophere}</li>
                        </ul>

                        <input type='text' id='field' name="expencesreport[dimension][]" value=''>
                    </div>
                </div>


                <div style="display:block;">
                    <div style="display:inline-block; padding: 8px; margin:8px;">   <input value="{$lang->generate}"  class="button" type="submit" id="attendance\/leavexpensesreport_Button"/></div>
                </div>


            </form>
        </td>
    </tr>



</body>

</html>

