<div id="popup_creteapplication"  title="{$lang->create}">
    <script>
        $(function () {
            var icons = {
                header: "ui-icon-circle-arrow-e",
                activeHeader: "ui-icon-circle-arrow-s"
            };
            $("#tabs").tabs();
            $("#accordion").accordion(
                    {
                        heightStyle: "content",
                        icons: icons
                    });
        });
    </script>
    <form action="#" method="post" id="perform_products/applications_Form" name="perform_products/applications_Form">
        <input type="hidden" name="action" value="do_create" />
        <input type="hidden" name='segmentapplications[psaid]' value='{$application->psaid}'/>
        <div id="tabs"> <!--templ$eventidate-->
            <ul>
                <li><a href="#tabs-1">{$lang->manageappl}</a></li>
                <li><a href="#tabs-2">{$lang->translation}</a></li>
            </ul>
            <div id="loadindsection"></div>
            <div id = "tabs-1">
                <table cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                        <td width="40%"><strong>{$lang->name}</strong></td><td> <input name="segmentapplications[title]" value='{$application->title}' type="text"/></td>
                    </tr>
                    <tr>
                        <td width="40%"><strong>{$lang->segment}</strong></td><td>{$segments_list}</td>
                    </tr>
                    <tr>
                        <td width="40%"><strong>{$lang->sequence}</strong></td><td><input name="segmentapplications[sequence]" value='{$application->sequence}' min="0" type="number"/></td>
                    </tr>
                    <tr>
                        <td colspan="2">{$lang->description}<br /><textarea name="segmentapplications[description]" id='description{$application->psaid}' cols="50" class="basictxteditadv" rows="25">{$application->description}</textarea></td>
                    </tr>
                    <tr>
                        <td>{$lang->publishonweb}</td><td>{$publishonwebcheckbox}</td>
                    </tr>
                    <tr><td colspan="2"><hr /></td></tr>
                    <tr>
                        <td><strong>{$lang->functions}</strong></td><td><select name="segmentapplications[segappfunctions][]" multiple="true">{$checmicalfunctions_list}</select></td>
                    </tr>

                </table>
            </div>
            <div id = "tabs-2">
                {$translationsprodapp}
            </div>
        </div>
        <div>
            <hr />
            <input type='button' id='perform_products/applications_Button' value='{$lang->savecaps}' class='button'/>
            <div id="perform_products/applications_Results"></div>
        </div>
    </form>
</div>