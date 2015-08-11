<div id="popup_addsegmentcat" title="{$lang->addasegment}">
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
    <form id="add_products/segmentcategory_Form" name="add_products/segmentcategory_Form" action="#" method="post">
        <div id="tabs"> <!--templ$eventidate-->
            <ul>
                <li><a href="#tabs-1">{$lang->managesegmentcat}</a></li>
                <li><a href="#tabs-2">{$lang->translation}</a></li>
            </ul>
            <div id="loadindsection"></div>
            <div id = "tabs-1">
                <table cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td width="20%">{$lang->title}<input type='hidden' name='segmentcat[scid]' value='{$segmentcat[scid]}'/></td>
                        <td width="80%"><input type="text" id="title" name="segmentcat[title]" value="{$segmentcat[title]}"/></td>
                    </tr>
                    <tr>
                        <td colspan="2">{$lang->description}<br /><textarea cols="30" rows="5" id="description{$segmentcat[scid]}" name="segmentcat[description]" class="basictxteditadv">{$segmentcat['description']}</textarea></td>
                    </tr>
                    <tr>
                        <td>{$lang->sequence}</td><td><input type="text" size="4" id="sequence" name="segmentcat[featuredSequence]" value="{$segmentcat[featuredSequence]}"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->largebanner}</td><td><input type="text" size="50" id="largeBanner" name="segmentcat[largeBanner]" value="{$segmentcat[largeBanner]}"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->mediumbanner}</td><td><input type="text" size="50" id="mediumBanner" name="segmentcat[mediumBanner]" value="{$segmentcat[mediumBanner]}"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->smallbanner}</td><td><input type="text" size="50" id="smallBanner" name="segmentcat[smallBanner]" value="{$segmentcat[smallBanner]}"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->shortdesc}</td><td><textarea id="shortDescription" cols="50" rows="5" name="segmentcat[shortDescription]">{$segmentcat[shortDescription]}</textarea></td>
                    </tr>
                    <tr>
                        <td>{$lang->slogan}</td><td><textarea id="slogan" cols="50" rows="5" name="segmentcat[slogan]">{$segmentcat['slogan']}</textarea></td>
                    </tr>
                    <tr>
                        <td>{$lang->brandingcolor}</td><td><input id="brandingColor" type="color" name='segmentcat[brandingColor]' value="{$segmentcat[brandingColor]}"></td>
                    </tr>
                    <tr>
                        <td>{$lang->publishonweb}</td><td><input type="checkbox" {$publish_checked} id="category" name="segmentcat[publishOnWebsite]" value="1"/></td>
                    </tr>
                    <tr>
                        <td>{$lang->includeinwebcarousel}</td><td><input type="checkbox" {$carousel_checked} id="category" name="segmentcat[includeInWebsiteCarousel]" value="1"/></td>
                    </tr>
                </table>
            </div>
            <div id = "tabs-2">
                {$translationsprodseg}
            </div>
        </div>
        <input type="button" id="add_products/segmentcategory_Button" value="{$lang->add}" class="button" /><input type="reset" value="{$lang->reset}" class="button" />
        <div id="add_products/segmentcategory_Results"></div>
    </form>
</div>