<div>
    <div style="display: inline-block; width: 50%"><h1>{$requirement[title]}</h1></div>
    <div style="display: inline-block; width: 50%; float: right; text-align: right;">{$complete_button}</div>
</div>

{$lang->reference}: {$requirement[refWord]} {$parent[refKey]}{$reference_sep}{$requirement[refKey]}<br />
{$lang->parent}: <a href="index.php?module=development/viewrequirement&id={$requirement[parent]}" target="_blank">{$requirement[parentTitle]}</a><br />
<p>{$requirement[description]}</p>
<p><span class="subtitle">{$lang->security}</span><br />{$requirement[security]}</p>
<p><span class="subtitle">{$lang->interface}</span><br />{$requirement[userInterface]}</p>
<p><span class="subtitle">{$lang->performance}</span><br />{$requirement[performance]}</p>
<hr />
{$children_list}
<hr />
{$changes_section}