<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillmontlyreport}</title>
        {$headerinc}
        <style>
            #dimensionfrom, #dimensionto {
                list-style-type: none;
                margin: 5;
                padding: 0;
                float: left;
                margin-right: 10px;
                -webkit-border-radius: 5px 4px 7px 0px;
                -moz-border-radius:  1px 4px 7px 0px;
                border-radius: 1px 4px 7px 0px;
                border: 2px  dashed  #cccccc;
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

            .sortable {
                list-style-type: none;
                margin: 10px;
                float: left;
                border-radius: 1px;
                border: 1px dashed #CCC;
                background: none repeat scroll 0% 0% #FEFEFE;
                padding: 5px;
                width: 50%;
            }

        </style>
    </head>
    <body>
        {$header}

        <script>
            $(function() {
                $("#dimensionfrom, #dimensionto")
                $("#dimensionfrom, #dimensionto").sortable({
                    connectWith: ".sortable",
                    revert: true, //revert to their new positions using a smooth animation.
                    cursor: "wait",
                    tolerance: "intersect", //overlaps the item being moved to the other item by 50%.
                    placeholder: "ui-state-highlight",
                    over: function() {
                        $('.sortable-placeholder').hide();
                    },
                    dropOnEmpty: true, //Prevent all items in a list from being dropped into a separate, empty list
                    start: function() {        /*  return back the Color of the  element to its origin Upon remove of the item */
                        $("#dimensionto li").animate({
                            opacity: 2.35,
                            backgroundColor: "#cccccc",
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
            <form action="index.php?module=crm/marketintelligence_report_preview_obj&amp;referrer=generate&amp;identifier={$identifier}" method="post" id="perform_crm\/marketintelligencereport_Form" name="perform_crm/marketintelligencereport_Form">
                <div class="thead" >{$lang->filters}</div>

                <table width="100%">
                    <tr>
                        <td width="50%">
                            <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                <table class="datatable" width="100%">
                                    <thead >
                                        <tr>
                                            <th>{$lang->affiliate}  <input class='inlinefilterfield' tabindex="1" type='text' style="width:70%" placeholder="{$lang->searchaff}"/></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$affiliates_list}
                                    </tbody>
                                </table>
                            </div>
                        </td>
                        <td  width="50%">
                            <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                <table class="datatable" width="100%">
                                    <thead >
                                        <tr>
                                            <th>{$lang->supplier}  <input class='inlinefilterfield' type='text'tabindex="2" style="width:70%" placeholder="{$lang->searchsupp}"/></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$suppliers_list}
                                    </tbody>
                                </table>
                            </div>
                        </td>

                    </tr>
                    <tr>
                        <td  width="50%">
                            <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                <table class="datatable" width="100%">
                                    <thead >
                                        <tr>
                                            <th>{$lang->customer}  <input class='inlinefilterfield' type='text' tabindex="3" style="width:70%" placeholder="{$lang->searchcust}"/></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$customers_list}
                                    </tbody>
                                </table>
                            </div>
                        </td>


                        <td colspan="2"  width="50%">
                            <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                <table class="datatable" width="100%">
                                    <thead >
                                        <tr>
                                            <th>{$lang->segment}  <input class='inlinefilterfield' type='text' tabindex="4" style="width:70%" placeholder="{$lang->searchseg}"/></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$segmentlist}
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3"  width="50%">
                            <div style="width:30%; height:70px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                <table class="datatable" width="100%">
                                    <thead >
                                        <tr>
                                            <th>{$lang->customertypes} </th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="checkbox" name="mireport[filter][ctype][]"  value="pc"/>{$lang->potential}</td>
                                            <td><input type="checkbox" name="mireport[filter][ctype][]"  value="c"/>{$lang->customer} </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="thead" style="margin:10px;">{$lang->dimensions}</div>
                <div style="display:block; ">
                    <div style="display:inline-block;width:40%;  vertical-align:top;">
                        <ul id="dimensionfrom"   class="connectedSortable">
                            {$dimension_item}
                        </ul>
                    </div>
                    <div style="display:inline-block;width:40%; vertical-align:top;">
                        <div style="text-align: left;">
                            {$lang->selecteddimensions}<br />
                            <ul id="dimensionto" class="sortable">
                                <li class="sortable-placeholder" style="background:none;">{$lang->drophere}</li>
                            </ul>
                        </div>
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
    {$footer}
</body>
</html>