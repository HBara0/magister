<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->generateforecast}</title>
        {$headerinc}


        <script type="text/javascript">
            $(function() {
                $(document).on('change', "select[id='forecast[reporttype]']", function() {
                    var id = $(this).attr("id")
                    var value = $(this).val();
                    $("div[id$=_reporttype]").not([id ^= '"+$(this).val()+"']).hide();
                    $("div[id^='" + value + "']").show(1000);
                });
            });
        </script>

    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->generateforecast}</h1>
            <form name="perform_grouppurchase/generate_Form" id="perform_grouppurchase/generate_Form" method="post" action="index.php?module=grouppurchase/previewforecast">
                <input type="hidden" name="identifier" value="{$core->input[identifier]}"/>
                <div style="display:block; padding:8px;">
                    <table width="100%">
                        <div class="thead" >{$lang->filterby}</div>

                        <tr>
                            <td  width="50%">
                                <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                    <table class="datatable" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="100%"><input type="checkbox" id='affiliatefilter_checkall'><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->affiliate}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                            {$affiliates_list}
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                            <td  width="50%">
                                <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                    <table class="datatable" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="100%"><input type="checkbox" id='supplierfilter_checkall'><input class='inlinefilterfield' type='text' tabindex="2" placeholder="{$lang->search} {$lang->supplier}" style="width:70%;display:inline-block;margin-left:5px;"/></th>
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
                                <div style="width:100%; height:70px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                                    <table class="datatable" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="100%">{$lang->year}<input class='inlinefilterfield' type='text' placeholder="{$lang->search} {$lang->year}" style="width:70%;display:inline-block;margin-left:5px;"/></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {$forecast_year_list}
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                            <td  width="50%">
                                <div style="width:100%; height:70px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">

                                    <table class="datatable" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="100%">{$lang->reporttype}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><select  name="forecast[reporttype]" id="forecast[reporttype]" style="width:100%;">
                                                        <option value="basic">{$lang->basictabular}</option>
                                                        <option value="dimensional">{$lang->dimensional}</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="dimensional_reporttype" style="display:none;">
                    <div class="thead" style="margin:10px;">{$lang->dimensions}</div>
                    <div style="display:block; ">
                        <div style="display:inline-block;width:40%;  vertical-align:top;">
                            <ul id="dimensionfrom" class="sortable">
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
                            <input type='hidden' id='dimensions' name="forecast[dimension][]" value=''>
                        </div>
                    </div>
                </div>
                <div  id="budget_currspecify"style="display:block;">
                    <input type="submit" value="{$lang->generate}" class="button"> <input type="reset" value="{$lang->reset}" class="button">
                </div>
            </form>
        </td>
    </tr>
</body>
</html>