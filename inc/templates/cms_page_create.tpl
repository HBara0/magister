<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->modifysitesettings}</title>
        {$headerinc}
        <script type="text/javascript">
            $(document).ready(function() {

                $('.texteditor').redactor({imageUpload: rootdir + '/index.php?module=cms/managewebpage&action=do_uploadtmpimage', imageUploadCallback:
                function(obj, json) { $('#uploadedImages').val(json.filelink + ';' + $('#uploadedImages').val());}
                });
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->pagetitle}</h3>
            <div style="display:table; width: 100%;">
                <form  name="perform_cms/managewebpage_Form" action="#" method="post"  id="perform_cms/managewebpage_Form" >
                    <input type="hidden" value="do_{$actiontype}page" name="action" id="action" />
                    <div style="display:block;width:100%;">
                        <div style="display:inline-block; padding:11px;">{$lang->title}</div>
                        <div style="display:inline-block; padding:11px;">
                            <input type="text" name="page[title]"   size="40" required="required" value="{$page[title]}"/>
                        </div>
                    </div>

                    <div style="display:block;">
                        <div style="display:inline-block; padding:11px;">{$lang->alias}</div>
                        <div style="display:inline-block; padding:11px;"> <input type="text" size="40" name="page[alias]"  value="{$page[alias]}"required="required"/></div>
                    </div>

                    <div style="display:block;">
                        <div style="display:inline-block; padding:11px;">{$lang->tags}</div>
                        <div style="display:inline-block; padding:11px;">  <textarea name="page[tags]" title="You can add multiple tag seperated by comma" cols="50"  rows="3">{$page[tags]}</textarea></div>
                    </div>


                    <div style="display:block;">
                        {$publish_page}
                    </div>

                    <div style="display:block;">
                        <div style="display:inline-block; padding:11px;"> {$lang->lang}</div>
                        <div style="display:inline-block; padding:11px;">
                            <select name="page[lang]">
                                <option value="english">{$lang->english}</option>
                                <option value="french">{$lang->french}</option>
                            </select></div>
                    </div>
                    <div style="display:block;margin:5px;">
                        <div style="display:inline-block; padding:11px;"> {$lang->categories}</div>
                        <div style="display:inline-block; padding:11px;"> {$pagecategories_list}</div>
                    </div>

                    <div style="display:block;">
                        <div style="display:inline-block; padding:11px;">{$lang->publishdate}</div>
                        <div style="display:inline-block; padding:11px;"><input name="page[publishDate]" type="text" id="pickDate" size="30" value="{$page[publishDate_output]}"> </div>
                    </div>
                    <div style="display:block;"  class="thead"> {$lang->pagetext}</div>
                    <div style="display:block;"> <textarea name="page[bodyText]" cols="90" class="texteditor" rows="25">{$page[bodyText]}</textarea>  <input type="hidden" name="page[uploadedImages]" id="uploadedImages" value="{$page[uploadedImages]}">
                    </div>



                    <div class="thead" style="margin-top:8px;">{$lang->metadata}</div>

                    <div style="display:block;">
                        <div style="display:inline-block; padding:11px;">{$lang->metadesc}</div>
                        <div style="display:inline-block; padding:11px; vertical-align: top;">
                            <textarea name="page[metaDesc]" cols="60" rows="3" wrap="soft">{$page[metaDesc]}</textarea>
                        </div>
                    </div>

                    <div style="display:block;">
                        <div style="display:inline-block; padding:11px;">{$lang->metakeyword}</div>
                        <div style="display:inline-block;  padding:11px;vertical-align: middle;">
                            <textarea name="page[metaKeywords]" cols="60" rows="3">{$page[metaKeywords]}</textarea>
                        </div>
                    </div>


                    <div style="display:block;">
                        <div style="display:inline-block; padding:11px;">{$lang->robots}</div>
                        <div style="display:inline-block; padding:11px;">{$robots_list}</div>
                    </div>
                    <div style="display:block;">
                        <div style="display:inline-block; padding:11px;"> <input type="submit"  class="button" value="{$actiontype}" id="perform_cms/managewebpage_Button"/></div>
                        <div style="display:inline-block; padding:11px;"><input type="reset"  class="button" value="{$lang->reset}"/></div>

                    </div>

                </form>
                <div style="display:block;">
                    <div style="display:inline-block; padding:11px;" id="perform_cms/managewebpage_Results"></div>
                </div>
            </div>

        </td>
    </tr>
    {$footer}

</body>
</html>