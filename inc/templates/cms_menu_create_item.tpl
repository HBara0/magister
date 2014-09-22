<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->modifysitesettings}</title>
        {$headerinc}
        <script type="text/javascript">
            $(function() {

                $('.texteditor').redactor({imageUpload: rootdir + '/index.php?module=cms/managemenu&action=do_uploadtmpimage', imageUploadCallback:
                function(obj, json) { $('#uploadedImages').val(json.filelink + ';' + $('#uploadedImages').val());}
                });

                $("input[type='radio'][id$='_type']").live('change', function() {
                    var id = $(this).attr("id");

                    /*	if($(this).not($("div[id^='" + $(this).val() + "']"))) {
                     alert('hideee');
                     }// hide*/
                    /*go throw each select and input in hte main configuration  div we are hiding and  reset their value */
                    $("div[id$=_configuration]").not([id ^= '" + $(this).val() + "']).find("select,input").each(function() {
                        $(this).val(''), $(this).addClass("thead");
                    });
                    $("div[id$=_configuration]").not([id ^= '" + $(this).val() + "']).hide();
                    $("div[id^='" + $(this).val() + "']").show(1000);

                });
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$managemenuitem} <span class="subtitle">{$menuname}</span>
            </h3>
            <div style="display:table;-webkit-box-shadow: inset 2px 2px 5px 2px #F1F1F1;">

                <form   name="perform_cms/managemenu_Form" method="post"  id="perform_cms/managemenu_Form" >
                    <input type="hidden" value="do_{$actiontype}menuitem" name="action" id="action" />
                    <input type="hidden" value="{$menu_id}" name="menuitem[cmsmid]" id="menuitem[menuid]" />
                    <input type="hidden" value="{$submenu_id}" name="menuitem[itemid]" />
                    <div style="display:table-row">
                        <div style="display: table-cell; width:90px;">{$lang->title}</div>
                        <div style="display: table-cell; padding:5px; ">
                            <input name="menuitem[title]" type="text" value="{$menuitem[title]}" required="required" size="30">
                        </div>
                    </div>

                    <div style="display:table-row;">
                        <div style="display:table-cell;">{$lang->alias}</div>
                        <div style="display: table-cell; padding:5px;">
                            <input name="menuitem[alias]" type="text" value="{$menuitem[alias]}" required="required" size="30">
                        </div>
                    </div>


                    <div style="display:table-row ;">
                        <div style="display:table-cell;">{$lang->menuname}</div>
                        <div style="display:table-cell;">{$menuname}</div>
                    </div>

                    <div style="display:table-row">
                        <div style="display:table-cell;">{$lang->lang}</div>
                        <div style="display: table-cell; padding:5px;">

                            <select name="menuitem[lang]">
                                <option value="english">{$lang->english}</option>
                                <option value="french">{$lang->french}</option>
                            </select></div>
                    </div>

                    <div style="display:table-row">
                        <div style="display:table-cell;">{$lang->parent}</div>
                        <div  style="display: table-cell; padding:5px;">{$parent_list}</div>
                    </div>

                    <div style="display:table-row">
                        <div style="display:table-cell;">{$lang->ispublish}</div>
                        <div  style="display: table-cell; padding:5px;"><input name="menuitem[isPublished]" type="checkbox" value='1'{$checkedboxes[isPublished]}></div>
                    </div>

                    <div class="thead">{$lang->configuration}</div>
                    <div  class="altrow2" style="display:table-row; width:100%;">
                        <div style="display:table-cell;">{$lang->menutypes}</div>

                        <div style="display:table-cell; padding:2px;">
                            <div style="display:inline-block; margin-left:30px">
                                <input  id="webpage_type" name="menuitem[type]" type="radio" value="webpage">
                                <label>{$lang->webpage}</label>
                            </div>
                            <div style="display:inline-block; margin-left:30px">
                                <input id="branchprofile_type" name="menuitem[type]" type="radio" value="branchprofile">
                                <label>{$lang->affiliateprofile}</label>
                            </div>
                            <div style="display:inline-block; margin-left:30px">
                                <input id="listaffiliates_type"  name="menuitem[type]" type="radio" value="listaffiliates">
                                <label>{$lang->listaffiliates}</label>
                            </div>
                            <div style="display:inline-block; margin-left:30px">
                                <input  id="externalurl_type" name="menuitem[type]" type="radio" value="externalurl"  >
                                <label>{$lang->externalurl}</label>
                            </div></div>


                        <div style="display:table-cell; padding:5px; margin:5px;">
                            <div style="display:inline-block; margin-left:30px">
                                <input id="contact_type" name="menuitem[type]" type="radio" value="contact">
                                <label>{$lang->contact}</label>
                            </div>
                            <div style="display:inline-block; margin-left:30px">
                                <input id="jobs_type" name="menuitem[type]" type="radio" value="jobs">
                                <label>{$lang->jobs}</label>
                            </div>
                            <div style="display:inline-block; margin-left:30px">
                                <input id="listsegments_type" name="menuitem[type]" type="radio" value="segmentslist">
                                <label>{$lang->listsegments}</label>
                            </div>
                            <div style="display:inline-block; margin-left:30px">
                                <input  id="singlesegment_type" name="menuitem[type]" type="radio" value="singlesegment">
                                <label>{$lang->segment}</label>
                            </div>
                        </div><!--end 2st type table cell-->


                        <div style="display:table-cell; padding:5px; margin:5px;">
                            <div style="display:inline-block; margin-left:30px">
                                <input  id="listnews_type"name="menuitem[type]" type="radio" value="news">
                                <label>{$lang->listnews}</label>
                            </div>
                            <div style="display:inline-block; margin-left:30px">
                                <input id="events_type"  name="menuitem[type]" type="radio" value="events">
                                <label>{$lang->listevents}</label>
                            </div>
                            <div style="display:inline-block; margin-left:30px">
                                <input id="newsarchive_type" name="menuitem[type]" type="radio" value="newsarchive">
                                <label>{$lang->newsarchive}</label>
                            </div>
                            <div style="display:inline-block; margin-left:30px">
                                <input id="eventsarchive_type" name="menuitem[type]" type="radio" value="eventsarchive">
                                <label>{$lang->eventsarchive}</label>
                            </div>
                        </div><!--end 2st type table cell-->

                    </div>

                    <div  id="webpage_configuration" style="display:display:block;">
                        <div style="display: table-cell; padding:10px;">
                            <fieldset class="altrow2" style="width:100%;">
                                <legend class="subtitle">{$lang->webpage}</legend>
                                <div style="display: table-cell; padding:10px;">{$lang->publishedpage} </div>
                                <div style="display: table-cell;  margin-left:10px;" id="webpage_content"> {$list_webpages} </div>
                            </fieldset>
                        </div>
                    </div>
                    <div id="contact_configuration" style="display:none;">
                        <div style="display: table-cell; padding:10px;">
                            <input type="hidden" name="menuitem[configurations][contact][]"  value="contact" />
                        </div>
                    </div>
                    <div id="jobs_configuration" style="display:none;">
                        <div style="display: table-cell; padding:10px;">
                            <input type="hidden" name="menuitem[configurations][jobs][]"  value="jobs" />
                        </div>
                    </div>
                    <div id="listaffiliates_configuration" style="display:none;">
                        <div style="display: table-cell; padding:10px;">
                            <fieldset class="altrow2" style="width:100%;">
                                <legend class="subtitle">{$lang->listaffiliates}</legend>
                                <div style="display: table-cell; padding:10px;">{$lang->exclude} </div>
                                <div style="display: table-cell;  margin-left:10px;"id="listaffiliates_content"> {$list_affiliates} </div>
                            </fieldset>
                        </div>
                    </div>
                    <div id="segmentslist_configuration" style="display:none;">
                        <div style="display: table-cell; padding:10px;">
                            <fieldset class="altrow2" style="width:100%;">
                                <legend class="subtitle">{$lang->listsegments}</legend>
                                <div style="display: table-cell; padding:10px;">{$lang->exclude}</div>
                                <div style="display: table-cell;  margin-left:10px;"id="segmentslist_content"> {$list_segments} </div>
                            </fieldset>
                        </div>

                    </div>
                    <div id="singlesegment_configuration" style="display:none;">
                        <div style="display: table-cell; padding:10px;">
                            <fieldset class="altrow2" style="width:100%;">
                                <legend class="subtitle">{$lang->listsegments}</legend>
                                <div style="display: table-cell; padding:10px;">{$lang->singlesegment}</div>
                                <div style="display: table-cell;  margin-left:10px;"id="singlesegment_content"> {$single_segment} </div>
                            </fieldset>
                        </div>

                    </div>
                    <div id="branchprofile_configuration" style="display:none;">
                        <div style="display: table-cell; padding:10px;">
                            <fieldset class="altrow2" style="width:100%;">
                                <legend class="subtitle">{$lang->brancheprofile}</legend>
                                <div style="display: table-cell; padding:10px;">{$lang->affiliate} </div>
                                <div style="display: table-cell;  margin-left:10px;"id="branchprofile_content"> {$list_brancheprofile} </div>
                            </fieldset>
                        </div>
                    </div>

                    <div id="externalurl_configuration" style="display:none;">
                        <div style="display: table-cell; padding:10px;">
                            <fieldset class="altrow2" style="width:100%;">
                                <legend class="subtitle">{$lang->externalurl}</legend>
                                <div  style="display:table-row;">
                                    <div style="display: table-cell; padding:10px;">{$lang->link} </div>
                                    <div style="display: table-cell; padding:10px;"><input name="menuitem[configurations][link]" type="url" ></div>
                                </div>

                                <div  style="display:table-row">
                                    <div style="display: table-cell; padding:10px; ">{$lang->linktitle} </div>

                                    <div style="display: table-cell; padding:10px; margin-left:20px;"><input name="menuitem[configurations][linktitle]" type="text"></div>
                                </div>
                                <div  style="display:table-row">
                                    <div style="display: table-cell; padding:10px;">{$lang->linkimage} </div>

                                    <div style="display: table-cell; padding:10px;margin-left:20px;"><input  name="menuitem[configurations][linkimage]" type="text"></div>
                                </div>

                            </fieldset>
                        </div>
                    </div>

                    <div class="thead">{$lang->metadata}</div>
                    <div style="display:table-row">
                        <div style="display:table-cell;">{$lang->metadesc}</div>
                        <div style="display:table-cell; padding:5px;">

                            <textarea name="menuitem[metaDesc]" cols="40" rows="3" wrap="soft">{$menuitem[metaDesc]}</textarea>
                        </div>
                    </div>

                    <div style="display:table-row">
                        <div style="display:table-cell; vertical-align:text-top">{$lang->metakeyword}</div>
                        <div style="display:table-cell; padding:5px;">
                            <textarea name="menuitem[metaKeywords]" cols="40" rows="3">{$menuitem[metaKeywords]}</textarea>
                        </div>

                    </div>
                    <div style="display:table-row">
                        <div style="display:table-cell; padding:5px;">{$lang->robots}</div>
                        <div style="display:table-cell; padding:5px;">{$robots_list}</div>
                    </div>

                    <div style="display:table-row">
                        <div style="display:table-cell;">{$lang->publishedDesc}</div>
                        <div style="display: table-cell; padding:5px;">
                            <textarea name="menuitem[publishedDesc]" cols="50" class="texteditor" rows="15">{$menuitem[publishedDesc]}</textarea>
                        </div>

                    </div>

                    <div style="display:table-row">
                        <div style="display:table-cell;"> <input type="submit" class="button" value="{$lang->create}" id="perform_cms/managemenu_Button"/></div>
                        <div  style="display: table-cell;"><input type="reset"  class="button" value="{$lang->reset}"/></div>

                    </div>

                </form>
                <div style="display:table-row">
                    <div style="display:table-cell;"id="perform_cms/managemenu_Results"></div>
                </div>
            </div>

        </td>
    </tr>
    {$footer}
</body>
</html>