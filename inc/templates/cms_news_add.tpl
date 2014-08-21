<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->modifysitesettings}</title>
        {$headerinc}
        <script type="text/javascript">
            $(document).ready(function() {
                $('.texteditor').redactor({imageUpload: rootdir + '/index.php?module=cms/managenews&action=do_uploadtmpimage', imageUploadCallback: function(obj, json) { $('#uploadedImages').val(json.filelink + ';' + $('#uploadedImages').val());} });
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->managenews}</h1>
            <iframe id='uploadFrame' name='uploadFrame' src='#' style="display:none;"></iframe>
            <div>
                <form action="index.php?module=cms/managenews" method="post" enctype="multipart/form-data" name="cms_addnews_Form" id="cms_addnews_Form" target="uploadFrame">
                    <input type="hidden" value="do_{$actiontype}news" name="action" id="action" />
                    <div style="display:table-row;">
                        <div style="display: table-cell; width: 30%;">{$lang->newstitle}</div>
                        <div style="display: table-cell; padding:10px;"><input name="news[title]" type="text" value="{$news[title]}" required="required" size="30"></div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display:table-cell;">{$lang->alias}</div>
                        <div style="display: table-cell; padding:10px;"><input name="news[alias]" type="text" value="{$news[alias]}" required="required"  size="30"></div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display:table-cell;">{$lang->tags}</div>
                        <div style="display: table-cell; padding:10px;">           <textarea name="news[tags]" cols="30"  rows="3">{$news[tags]}</textarea></div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display:table-cell;">{$lang->date}</div>
                        <div style="display: table-cell; padding:10px;"><input name="news[publishDate]" type="text" id="pickDate" size="30" value="{$news[publishDate_output]}"></div>
                    </div>
                    <div style="display:table-row;">
                        {$publish_news}
                    </div>
                    <div style="display:table-row;">
                        <div style="display: table-cell;">{$lang->featurenews}</div>
                        <div style="display: table-cell; padding:10px;"><input name="news[isFeatured]" type="checkbox" value="1"{$checkedboxes[isFeatured]}></div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display:table-cell;">{$lang->newslang}</div>
                        <div style="display: table-cell; padding:10px;">
                            <select name="news[lang]">
                                <option value="english"{$seletcted[en]}>{$lang->english}</option>
                                <option value="french"{$seletcted[fr]}>{$lang->french}</option>
                            </select>
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display:table-cell;">{$lang->categories}</div>
                        <div style="display: table-cell; padding:10px;">{$newscategories_list}</div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display:table-cell;">{$lang->summary}</div>
                        <div style="display: table-cell; padding:10px;"><textarea name="news[summary]" cols="50"rows="6">{$news[summary]}</textarea></div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display:table-cell;">{$lang->text}</div>
                        <div style="display: table-cell; padding:10px;">
                            <textarea name="news[bodyText]" cols="50" class="texteditor" rows="15" >{$news[bodyText]}</textarea>
                            <input type="text" name="news[uploadedImages]" id="uploadedImages" value="{$news[uploadedImages]}">
                        </div>
                    </div>
                    <div style="display:table-row;">
                        <div style="display:table-cell;">{$lang->attachfiles}</div>
                        <div style="display: table-cell; padding:10px;">
                            <fieldset  title="{$lang->attachments}"class="altrow2" style="border:1px solid #DDDDDD"><legend class="subtitle">{$lang->attachments}</legend><input type="file" id="attachments" name="attachments[]" multiple="true"></fieldset>
                        </div>

                    </div>
                    <div style="display:table-row;">
                        <div style="display:table-cell;">&nbsp;</div>
                        <div style="display: table-cell;"><input type="submit" onclick="$('#upload_Result').show()" class="button" id="cms_addnews_Form" value="{$actiontype}"  /> <input type="reset" class="button" value="{$lang->reset}"/></div>
                    </div>
                </form>
                <hr />
                <div id="upload_Result" style="display:none;"><img src="{$core->settings[rootdir]}/images/loading.gif" /> {$lang->uploadinprogress}</div>

            </div>
        </td>
    </tr>
    {$footer}
</body>
</html>