<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->extractentities}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->extractentities}</h1>
            <form action="#" method="post" id="perform_entities/extractentities_Form" name="perform_entities/extractentities_Form">
                <div class="form-group">
                    <label for="affiliate">{$lang->affiliate}</label>
                    <div id="affiliate" style="width:100%; height:200px; overflow:auto; vertical-align:top;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr class="altrow2">
                                    <th><input type="checkbox" id="affiliates_checkall"></th>
                                    <th><input class='inlinefilterfield' type='text' style="width: 95%" placeholder="{$lang->affiliate}" /></th>
                                </tr>
                            </thead>
                            <tbody>
                                {$affiliates_rows}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group">
                    <label for="segments">{$lang->segments}</label>
                    <div id="segments" style="width:100%; height:200px; overflow:auto; vertical-align:top;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr class="altrow2">
                                    <th><input type="checkbox" id="segments_checkall"></th>
                                    <th><input class='inlinefilterfield' type='text' style="width: 95%" placeholder="{$lang->segments}" /></th>
                                </tr>
                            </thead>
                            <tbody>
                                {$segments_rows}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group">
                    <label for="entype">{$lang->entitytype}</label>
                    <div class="checkbox" id="entype">
                        <label>
                            <input name="filters[type][]" value="'c'"  type="checkbox">{$lang->customer}
                        </label>
                        &nbsp;
                        <label>
                            <input name="filters[type][]" value="'s'" type="checkbox">{$lang->supplier}
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="extractype">{$lang->extractype}</label>
                    <div class="radio" id="extractype">
                        <!--     <label>
                               <input name="extract" value="generate"  type="radio" >{$lang->generate}
                           </label>
                           &nbsp;
                        -->
                        <label>
                            <input name="extract" value="export" type="radio" checked="checked">{$lang->export}
                        </label>
                    </div>
                </div>
                <input type="button" value="{$lang->submit}" id="perform_entities/extractentities_Button"/><input type="reset" value="{$lang->reset}" />
            </form>
            <div id="perform_entities/extractentities_Results"></div>
        </td>
    </tr>
    {$footer}
</body>
</html>