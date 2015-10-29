<h3>{$language->get_displayname()}</h3>
<div style="display:inline-block; width:70%;">
    <div style="display:block">
        <div style="display:block;" >{$lang->title}
            <input name="lang[{$lid}][title]" value='{$trans[$lid]['title']}' type="text"/></div>
    </div></br></br>
    <div style="display:block">
        <div style="display:block;" class="thead">{$lang->description}</div>
        <div style="display:block;"><textarea name="lang[{$lid}][description]" cols="100" rows="6" id='transegmentdescription_{$lid}' class="basictxteditadv">{$trans[$lid]['description']}</textarea></div>
    </div>
    </br></br>
    <div style="display:block">
        <div style="display:block;" class="thead">{$lang->slogan}</div>
        <div style="display:block;"><textarea name="lang[{$lid}][slogan]" cols="100" rows="6" >{$trans[$lid]['slogan']}</textarea></div>
    </div>
    </br></br>
    <div style="display:block">
        <div style="display:block;" class="thead">{$lang->shortdesc}</div>
        <div style="display:block;"><textarea name="lang[{$lid}][shortDescription]" cols="100" rows="6" id='transegmentshortDescription_{$lid}' class="basictxteditadv">{$trans[$lid]['shortDescription']}</textarea></div>
    </div>
</div>