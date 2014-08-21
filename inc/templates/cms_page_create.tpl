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
            <div style="display:table;-webkit-box-shadow: inset 2px 2px 5px 2px #F1F1F1;">
                <form  name="perform_cms/managewebpage_Form" action="#" method="post"  id="perform_cms/managewebpage_Form" >
                    <input type="hidden" value="do_{$actiontype}page" name="action" id="action" />
                    <div style="display:table-row">
                        <div style="display: table-cell; width:90px;">{$lang->title}</div>
                        <div style="display: table-cell; padding:5px; ">
                            <input type="text" name="page[title]"  required="required" value="{$page[title]}"/>
                        </div>
                    </div>

                    <div style="display:table-row">
                        <div style="display:table-cell;">{$lang->alias}</div>
                        <div style="display: table-cell; padding:5px;"> <input type="text" name="page[alias]"  value="{$page[alias]}"required="required"/></div>
                    </div>

                    <div style="display:table-row">
                        <div style="display:table-cell;">{$lang->tags}</div>
                        <div style="display: table-cell; padding:5px;">   <textarea name="page[tags]" title="You can add multiple tag seperated by comma" cols="40"  rows="3">{$page[bodyText]}</textarea></div>
                    </div>


                    <div style="display:table-row ;">
                        {$publish_page}  
                    </div>

                    <div style="display:table-row">
                        <div style="display:table-cell;">{$lang->lang}</div>
                        <div style="display: table-cell; padding:5px;">
                            <select name="page[lang]">
                                <option value="english">{$lang->english}</option>
                                <option value="french">{$lang->french}</option>
                            </select></div>
                    </div>
                    <div style="display:table-row">
                        <div style="display:table-cell;">{$lang->categories}</div>
                        <div  style="display: table-cell; padding:5px;">{$pagecategories_list}</div>
                    </div>

                    <div style="display:table-row">
                        <div style="display:table-cell;">{$lang->pagetext}</div>
                        <div style="display: table-cell; padding:5px;">
                            <textarea name="page[bodyText]" cols="50" class="texteditor" rows="15">{$page[bodyText]}</textarea>
                        </div>
                        <div style="display: table-cell; padding:5px;">
                            <input type="hidden" name="page[uploadedImages]" id="uploadedImages" value="{$page[uploadedImages]}">
                        </div>
                    </div>
                    <div style="display:table-row">
                        <div style="display:table-cell;">{$lang->publishdate}</div>
                        <div  style="display: table-cell; padding:5px;"> <input name="page[publishDate]" type="text" id="pickDate" size="30" value="{$page[publishDate_output]}"> </div>
                    </div>
                    <div class="thead">{$lang->metadata}</div>
                    <div style="display:table-row">
                        <div style="display:table-cell;">{$lang->metadesc}</div>
                        <div style="display:table-cell; padding:5px;">

                            <textarea name="page[metaDesc]" cols="40" rows="3" wrap="soft">{$page[metaDesc]}</textarea>
                        </div>
                    </div>

                    <div style="display:table-row">
                        <div style="display:table-cell; vertical-align:text-top">{$lang->metakeyword}</div>
                        <div style="display:table-cell; padding:5px;">
                            <textarea name="page[metaKeywords]" cols="40" rows="3">{$page[metaKeywords]}</textarea>
                        </div>
                    </div>


                    <div style="display:table-row">
                        <div style="display:table-cell; padding:5px;">{$lang->robots}</div>
                        <div style="display:table-cell; padding:5px;">{$robots_list}</div>
                    </div>
                    <div style="display:table-row">
                        <div style="display:table-cell;"> <input type="submit"  class="button" value="{$actiontype}" id="perform_cms/managewebpage_Button"/></div>
                        <div  style="display: table-cell;"><input type="reset"  class="button" value="{$lang->reset}"/></div>

                    </div>

                </form>
                <div style="display:table-row">
                    <div style="display:table-cell;"id="perform_cms/managewebpage_Results"></div>
                </div>
            </div>

        </td>
    </tr>
    {$footer}

</body>
</html>