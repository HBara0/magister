<div id="popup_addsegment" title="{$lang->addasegment}">
    <form id="add_products/segments_Form" name="add_products/segments_Form" action="#" method="post">
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
                <td>{$lang->largebanner}</td><td><input type="text" size="50" id="largeBanner" name="segment[largeBanner]" value="{$segment[largeBanner]}"/></textarea</td>
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
            <tr>
                <td colspan="2"><input type="button" id="add_products/segments_Button" value="{$lang->add}" class="button" /><input type="reset" value="{$lang->reset}" class="button" />
                    <div id="add_products/segments_Results"></div>
                </td>
            </tr>
        </table>
    </form>
</div>