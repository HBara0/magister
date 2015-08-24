<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listmenu}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
    <script>
        $(function () {
            function closedialog() {
                $("#popup_createmenu").dialog("destroy");
            }
            $(document).on('click', "a[id^=mainmenu_]", function () {
                var id = $(this).attr('rel'); /*take the id of clicked link*/
                $(this).data('newsid', id);
                contentId = "item_result_" + $(this).data('newsid');
                sharedFunctions.requestAjax("post", "index.php?module=cms/listmenu&action=viewmenuitem", {newsid:id}, '', contentId, false)

                $("div[id^=news_" + $(this).data('newsid') + "]").slideToggle("slow");
            }

            );

        });
    </script>
    <td class="contentContainer">
        <h3>{$lang->listmenu}</h3>
        <form action='index.php?module=cms/listmenu' method="post">
            <table class="datatable" width="100%">
                <thead>
                    <tr>
                        <th>{$lang->menutitle} <a href="{$sort_url}&amp;sortby=title&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=title&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>

                        <th>{$lang->menudesc}</th>
                        <th>{$lang->newsdate}<a href="{$sort_url}&amp;sortby=dateCreated&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="{$lang->sortasc}"/></a><a href="{$sort_url}&amp;sortby=dateCreated&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="{$lang->sortdesc}"/></a></th>

                        <th><a href="#" id="showpopup_createmenu" class="showpopup"><img alt="Add" src="./images/addnew.png" border="0"/> {$lang->createmenu}</a></th>

                    </tr>
                    {$filters_row}
                </thead>


                <tbody>
                    {$cms_menuitmes_list_rows}
                </tbody>
            </table>
        </form>
        <div style="width:40%; float:left; margin-top:20px;" class="smalltext"><form method='post' action='$_SERVER[REQUEST_URI]'>{$lang->perlist}: <input type='text' size='4' id='perpage_field' name='perpage' value='{$core->settings[itemsperlist]}' class="smalltext"/>
            </form></div>
        <div style="width:50%; float:right; display:none;margin-top:0px; text-align:right;" class="smalltext"><form method='post' action='index.php?module=cms/listmenu'>
                <select id="filterby" name="filterby">
                    <option value="title">{$lang->title}</option>
                </select> <input type="text" name="filtervalue" id="filtervalue"> <input type="submit" class="button" value="{$lang->filter}"></form></div>
    </td>
</tr>
{$footer}
{$deletemenuitem}
</body>
</html>



<div id="popup_createmenu"  title="{$lang->createmenu}">
    <form name='perform_cms/managemenu_Form' id="perform_cms/managemenu_Form" method="post">
        <input type="hidden" id="action" name="action" value="do_createmenu" />


        <div style="display:table-row">
            <div style="display:table-cell; width:90px;">{$lang->menutitle}</div>
            <div style="display:table-cell"><input name="menu[title]" type="text"  size="50" maxlength="30"></div>
        </div>

        <div style="display:table-row;">
            <div style="display:table-cell;width:90px; vertical-align:middle;">{$lang->menudesc}</div>
            <div style="display:table-cell; margin-top:5px;"><textarea name="menu[description]" cols="50" rows="3"></textarea></div>
        </div>
        <hr>
        <div style="display:table-row">
            <div style="display:table-cell">
                <input type="button" id="perform_cms/managemenu_Button" class="button" value="{$lang->add}"/>
                <input type="reset"  class="button" value="{$lang->reset}"/>
            </div>
        </div>

    </form>

    <div id="perform_cms/managemenu_Results" ></div>
</div>