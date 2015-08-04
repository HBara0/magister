<div id="popup_addsegment" title="{$lang->addasegment}">
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
    <form id="add_products/segments_Form" name="add_products/segments_Form" action="#" method="post">
        <div id="tabs"> <!--templ$eventidate-->
            <ul>
                <li><a href="#tabs-1">{$lang->managesegment}</a></li>
                <li><a href="#tabs-2">{$lang->translation}</a></li>
            </ul>
            <div id="loadindsection"></div>
            <div id = "tabs-1">
                <table cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td width="20%">{$lang->title}<input type='hidden' name='segment[psid]' value='{$segment[psid]}'/></td>
                        <td width="80%"><input type="text" id="title" name="segment[title]" value="{$segment[title]}"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->titleabbreviation}</td><td><input type="text" id="titleAbbr" name="segment[titleAbbr]" value="{$segment[titleAbbr]}"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->category}</td><td>{$category_selectlist}</td>
                    </tr>
                    <tr>
                        <td colspan="2">{$lang->description}<br /><textarea cols="30" rows="5" id="description{$segment[psid]}" name="segment[description]" class="basictxteditadv">{$segment['description']}</textarea></td>
                    </tr>
                    <tr>
                        <td>{$lang->sequence}</td><td><input type="text" size="4" id="sequence" name="segment[displaySequence]" value="{$segment[displaySequence]}"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->largebanner}</td><td><input type="text" size="50" id="largeBanner" name="segment[largeBanner]" value="{$segment[largeBanner]}"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->mediumbanner}</td><td><input type="text" size="50" id="mediumBanner" name="segment[mediumBanner]" value="{$segment[mediumBanner]}"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->smallbanner}</td><td><input type="text" size="50" id="smallBanner" name="segment[smallBanner]" value="{$segment[smallBanner]}"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->shortdesc}</td><td><textarea id="shortDescription" cols="50" rows="5" name="segment[shortDescription]">{$segment[shortDescription]}</textarea></td>
                    </tr>
                    <tr>
                        <td>{$lang->slogan}</td><td><textarea id="slogan" cols="50" rows="5" name="segment[slogan]">{$segment['slogan']}</textarea></td>
                    </tr>
                    <tr>
                        <td>{$lang->brandingcolor}</td><td><input id="brandingColor" type="color" name='segment[brandingColor]' value="{$segment[brandingColor]}"></td>
                    </tr>
                    <tr>
                        <td>{$lang->publishonweb}</td><td><input type="checkbox" {$checked} id="category" name="segment[publishOnWebsite]" value="1"/></td>
                    </tr>

                </table>
            </div>
            <div id = "tabs-2">
                {$translationsprodseg}
            </div>
        </div>
        <input type="button" id="add_products/segments_Button" value="{$lang->add}" class="button" /><input type="reset" value="{$lang->reset}" class="button" />
        <div id="add_products/segments_Results"></div>
    </form>
</div>