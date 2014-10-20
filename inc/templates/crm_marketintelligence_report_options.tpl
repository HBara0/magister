<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->fillmontlyreport}</title>
        {$headerinc}

    </head>
    <body>
        {$header}

    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->mireport}</h1>
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
                        <ul id="dimensionfrom"  class="sortable">
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