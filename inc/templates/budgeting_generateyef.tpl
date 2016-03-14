<script type="text/javascript">
            $(function() {
                $(document).on('change', 'input[name=fxrate]', function() {
                    $('#fxrateinput').slideToggle('fast', function() {
            });
        });
                $(document).on('change', "select[id='budget[reporttype]']", function() {
            var id = $(this).attr("id")
                    var value = $(this).val();
            //  if($(this).not($("div[id^='" + $(this).val() + "']"))) {

            $("div[id$=_reporttype]").not([id ^= '" + $(this).val() + "']).hide();
            $("div[id^='" + value + "']").show(1000);

        });

    });


</script>
<h1>{$lang->generate} {$lang->yearendforecast}</h1>
<form name="perform_budgeting/generateyearendforecast_Form" id="perform_budgeting/generateyearendforecast_Form" method="post">
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
                    <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr>
                                    <th width="100%"><input type="checkbox" id='bmfilter_checkall'><input class='inlinefilterfield' type='text' placeholder="{$lang->search} {$lang->bm}" style="width:60%;display:inline-block;margin-left:5px;"/></th>
                                </tr>
                            </thead>
                            <tbody style="height:100px;">
                                {$business_managerslist}
                            </tbody>
                        </table>
                    </div>
                </td>
                <td  width="50%">
                    <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr>
                                    <th width="100%"><input type="checkbox" id='segmentfilter_checkall'><input class='inlinefilterfield' type='text' placeholder="{$lang->search} {$lang->segment}" style="width:70%;display:inline-block;margin-left:5px;"/></th>
                                </tr>
                            </thead>
                            <tbody>
                                {$budget_segments_list}
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
                                {$budget_year_list}
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
                                    <td><select  name="budget[reporttype]" id="budget[reporttype]" style="width:100%;">
                                            <option value="basic">{$lang->basic}</option>
                                            <option value="dimensional">{$lang->dimensional}</option>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <strong>{$lang->currency}</strong><br />
                    {$currencies_list}
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
                <input type='hidden' id='dimensions' name="budget[dimension][]" value=''>
            </div>
        </div>
    </div>
    <input type="submit" id="perform_budgeting/generateyearendforecast_Button" value="{$lang->generatereport}" class="button"> <input type="reset" value="{$lang->reset}" class="button">


</form>
<div style="display:block;">
    <div id="perform_budgeting/generateyearendforecast_Results"></div>
</div>
