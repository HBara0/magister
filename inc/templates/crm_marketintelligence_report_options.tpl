<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillmontlyreport}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
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
                width: 50%;
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
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->mireport}</h3>
            <form action="index.php?module=crm/marketintelligence_report_preview&amp;referrer=generate&amp;identifier={$identifier}" method="post" id="perform_crm\/marketintelligencereport_Form" name="perform_crm/marketintelligencereport_Form">
                <div class="thead" style="margin:10px;">{$lang->filters}</div>
                <div style="display:block;width:100%;">
                    <div style="display:inline-block; padding:15px; margin:20px;vertical-align: top;">{$lang->affiliate}</div>
                    <div style="display:inline-block;"><select name="mireport[filter][affids][]" multiple="multiple">{$affiliates_list}</select></div>
                    <div style="display:inline-block; padding:15px; margin:20px;vertical-align:top;">{$lang->supplier}</div>
                    <div style="display:inline-block;"><select name="mireport[filter][spid][]"  multiple="multiple">{$suppliers_list}</select></div>


                </div>

                <div style="display:block;width:100%;">
                    <div style="display:inline-block; padding:15px; margin:20px;vertical-align: top;">{$lang->customer}</div>
                    <div style="display:inline-block;"><select name="mireport[filter][cid][]"  multiple="multiple">{$customers_list}</select></div>
                    <div style="display:inline-block;padding:15px; margin:20px;vertical-align: top;">{$lang->segment}</div>
                    <div style="display:inline-block; margin:20px;"><select name="mireport[filter][psid][]" multiple="multiple">{$segmentlist}</select></div>

                </div>

                <div style="display:block;padding: 8px;width:35%;">
                    <div style="display:inline-block; padding: 8px; margin:8px; vertical-align:top;">{$lang->bm}</div>
                    <div style="display:inline-block;"> {$business_managerslist}</div>

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

                        <input type='hidden' id='field' name="mireport[dimension][]" value=''>
                    </div>
                </div>

                <div style="display:block;">
                    <div style="display:inline-block; padding: 8px; margin:8px;">   <input value="{$lang->generate}"  class="button" type="submit" id="perform_crm/marketintelligencereport_Button"/></div>
                </div>

                <div style="display:block;">
                    <div id="perform_crm/marketintelligencereport_Results"></div>
                </div>
            </form>

        </td>
    </tr>
</body>
</html>