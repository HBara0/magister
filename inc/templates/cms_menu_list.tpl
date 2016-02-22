<script>
    $(function() {
        function closedialog() {
            $("#popup_createmenu").dialog("destroy");
        }
        $(document).on('click', "a[id^=mainmenu_]", function() {
            var id = $(this).attr('rel'); /*take the id of clicked link*/
            $(this).data('newsid', id);
            contentId = "item_result_" + $(this).data('newsid');
            sharedFunctions.requestAjax("post", "index.php?module=cms/listmenu&action=viewmenuitem", {newsid:id}, '', contentId, false)

            $("div[id^=news_" + $(this).data('newsid') + "]").slideToggle("slow");
        }

        );

    });
</script>
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
        {$createmenu}
        {$deletemenuitem}


