<div id="popup_applicationdescription" title="{$lang->description}">
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
    <form action="#" method="post" id="perform_products/functions_Form" name="perform_products/functions_Form">
        <input type="hidden" name="action" value="save_descr" />
        <input type='hidden' name='segfuncapp' value='{$safid}'/>
        <div id="tabs"> <!--templ$eventidate-->
            <ul>
                <li><a href="#tabs-1">{$lang->managesegapfunc}</a></li>
                <li><a href="#tabs-2">{$lang->translation}</a></li>
            </ul>
            <div id="loadindsection"></div>
            <div id = "tabs-1">
                <table>
                    <tr>
                        <td>{$lang->publishonweborunpub}</td>
                        <td>{$publishonwebsite}</td>
                    </tr>
                </table>
                <br>
                <textarea rows="5" cols="60" class="basictxteditadv" name="segapdescription" id="segapdescription{$safid}">{$segapdescriptions}</textarea>
                <hr />
            </div>
            <div id = "tabs-2">
                {$translationsapsegfunc}
            </div>
        </div>
        <input type='button' id='perform_products/functions_Button' value='{$lang->savecaps}' class='button'/>
        <div id="perform_products/functions_Results"></div>
    </form>
