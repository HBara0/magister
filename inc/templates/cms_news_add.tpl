<h1>{$lang->managenews}<br/>  <h4>{$news[version_output]}</h4></h1>
<iframe id='uploadFrame' name='uploadFrame' src='#' style="display:none;"></iframe>
<div>
    <form action="index.php?module=cms/managenews" method="post" enctype="multipart/form-data" name="cms_addnews_Form" id="cms_addnews_Form" target="uploadFrame">
        <input type="hidden" value="do_{$actiontype}news" name="action" id="action" />
        <input type="hidden" value="{$news[cmsnid]}" name="news[baseVersionId]"/>
        <div style="display:block;">
            <div style="display: inline-block;width:10%">{$lang->newstitle}</div>
            <div style="display: inline-block; padding:10px;"><input name="news[title]" type="text" value="{$news[title]}" required="required" size="100"><input name="news[alias]" type="hidden" value="{$news[alias]}" size="30"></div>
        </div>
        <div style="display:block;">
            <div style="display:inline-block;width:10%">{$lang->publishdate}</div>
            <div style="display: inline-block; padding:10px;"><input name="news[publishDate]" type="text" id="pickDate" size="30" value="{$news[publishDate_output]}"></div>
        </div>
        <div style="display:block;">
            {$publish_news}
        </div>
        <div style="display:block;">
            <div style="display: inline-block;width:10%">{$lang->featurenews}</div>
            <div style="display: inline-block; padding:10px;"><input name="news[isFeatured]" type="checkbox" value="1"{$checkedboxes[isFeatured]}></div>
        </div>
        <div style="display:block;">
            <div style="display:inline-block;width:10%">{$lang->newslang}</div>
            <div style="display: inline-block; padding:10px;">
                <select name="news[lang]">
                    <option value="english"{$seletcted[en]}>{$lang->english}</option>
                    <option value="french"{$seletcted[fr]}>{$lang->french}</option>
                </select>
            </div>
        </div>
        <div style="display:block;">
            <div style="display:inline-block;width:10%">{$lang->categories}</div>
            <div style="display: inline-block; padding:10px;">{$newscategories_list}</div>
        </div>
        <div style="{$baseversion[display]}">
            <div style="display:inline-block; padding:11px;">{$lang->baseversion}</div>
            <div style="display:inline-block; padding:11px;">{$news[baseVersion_outpt]}</div>
        </div>
        <div style="display:block;">
            <div style="display:block;" class="thead">{$lang->summary}</div>
            <div style="display:block;"><textarea name="news[summary]" cols="100"rows="6" id='summary' class="txteditadv">{$news[summary]}</textarea></div>
        </div>
        <div style="display:block;">
            <div style="display:block;" class="thead">{$lang->text}</div>
            <div style="display:block;">
                <textarea name="news[bodyText]" cols="100" id='bodyText' class="txteditadv" rows="15" >{$news[bodyText]}</textarea>
                <input type="hidden" name="news[uploadedImages]" id="uploadedImages" value="{$news[uploadedImages]}">
            </div>
        </div>
        <div style="display:block;">
            <div style="display:inline-block;width:10%">{$lang->tags}</div>
            <div style="display:inline-block; padding:10px;"><textarea name="news[tags]" cols="100" rows="3">{$news[tags]}</textarea></div>
        </div>
        <div class="thead" style="margin-top:8px;">{$lang->metadata}</div>
        <div style="display:block;">
            <div style="display:inline-block; padding:11px;">{$lang->metadesc}</div>
            <div style="display:inline-block; padding:11px; vertical-align: top;">
                <textarea name="news[metaDesc]" cols="60" rows="3" wrap="soft">{$news[metaDesc]}</textarea>
            </div>
        </div>
        <div style="display:block;">
            <div style="display:inline-block; padding:11px;">{$lang->metakeyword}</div>
            <div style="display:inline-block;  padding:11px; vertical-align: middle;">
                <textarea name="news[metaKeywords]" cols="60" rows="3">{$news[metaKeywords]}</textarea>
            </div>
        </div>
        <div style="display:block;">
            <div style="display:inline-block; padding:11px;">{$lang->robots}</div>
            <div style="display:inline-block; padding:11px;">{$robots_list}</div>
        </div>
        <div style="display:block;">
            <div class="thead" style="margin-top:8px;">{$lang->highlights}</div>
            {$higlightsbox}
        </div>
        <div style="display:block;">
            <div style="display:block;" class="thead">{$lang->attachfiles}</div>
            <div style="display:block;">
                <fieldset  title="{$lang->attachments}"class="altrow2" style="border:1px solid #DDDDDD"><legend class="subtitle">{$lang->attachments}</legend><input type="file" id="attachments" name="attachments[]" multiple="true"></fieldset>
            </div>
        </div>
        <div style="display:block;">
            <div style="display:inline-block;">&nbsp;</div>
            <div style="display:inline-block;">
                <input type="submit" class="button" id="cms_addnews_Form" value="{$actiontype}"  />
                <input type="reset" class="button" value="{$lang->reset}"/>
                <div style="{$preview_display};" id="preview"><a id="preview_link" target="_blank" href="{$url}"><button type="button" class="button">{$lang->preview}</button></a></div>
            </div>
        </div>
    </form>
    <hr />

</div>