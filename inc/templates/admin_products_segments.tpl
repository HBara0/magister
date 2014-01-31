<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listavailablesegments}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$lang->listavailablesegments}</h3>
            <div style="float:right;" class="subtitle"><a href="#" id="showpopup_addsegment" class="showpopup"><img src="{$core->settings[rootdir]}/images/addnew.png" border="0">{$lang->addasegment}</a></div>
            <table class="datatable">
                <thead>
                    <tr>
                        <th>{$lang->id}</th><th>{$lang->title}</th><th>{$lang->description} </th>
                    </tr>
                </thead>
                <tbody>
                    {$segments_list}
                </tbody>
            </table>
            <hr />
        </td>
    </tr>
    {$footer}
    <div id="popup_addsegment" title="{$lang->addasegment}">
        <form id="add_products/segments_Form" name="add_products/segments_Form" action="#" method="post">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="10%">{$lang->title}</td>
                    <td width="90%"><input type="text" id="title" name="title" /></td>
                </tr>
                <tr><td class="subtitle" width="10%">{$lang->coordinator}</td></tr>
                <tbody id="users_tbody">
                    {$users_rows}
                </tbody>
                <tr><td colspan="3"><img src="../images/add.gif" id="addmore_users" alt="{$lang->add}"><input type="hidden" name="users_numrows" id="numrows" value="{$users_counter}"></td><tr>
                <tr>
                    <td>{$lang->description}</td><td><textarea cols="30" rows="5" id="description" name="description"></textarea></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="button" id="add_products/segments_Button" value="{$lang->add}" /><input type="reset" value="{$lang->reset}" />
                        <div id="add_products/segments_Results"></div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>