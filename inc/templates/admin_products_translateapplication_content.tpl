<h3>{$language->get_displayname()}</h3>
<div style="display:inline-block; width:70%;">
    <div style="display:block">
        <div style="display:block;" >{$lang->name}
            <input name="lang[{$lid}][title]" value='{$trans[$lid]['title']}' type="text"/></div>
    </div></br></br>
    <div style="display:block">
        <div style="display:block;" class="thead">{$lang->description}</div>
        <div style="display:block;"><textarea name="lang[{$lid}][description]" cols="100" rows="6" id='transappdescription_{$lid}' class="basictxteditadv">{$trans[$lid]['description']}</textarea></div>
    </div>
</div>