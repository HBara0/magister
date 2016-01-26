<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->addcalllog}</title>
        {$headerinc}
        <script>  $(function() {
                $('.accordion .header').accordion({
                    collapsible: true,
                    active: false,
                });
                $('.accordion .header').click(function() {
                    $(this).next().toggle();
                    return false;
                }).next().hide();
                $('.accordion .header').trigger('click');
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->addcalllog}{$withentity}</h1>
            <form action="#" method="post" id="perform_crm/addcalllog_Form" name="perform_crm/addcalllog_Form" style="margin-bottom: 0px;">
                <div class="form-group">
                    {$entityid}
                </div>
                <br>
                <div class="form-group">
                    <label for="private"><strong>{$lang->isprivate}</strong></label>
                    <input type="checkbox" id="private"  name="log[isPrivate]" value="1">
                </div>
                <div class="form-group">
                    <label for="description"><strong>{$lang->calldescription}</strong></label>
                    <textarea id="description" class="htmltextedit" name="log[description]"></textarea>
                </div>
                <input type='submit' style="cursor: pointer;" class='button' value="{$lang->savecaps}" id='perform_crm/addcalllog_Button'>
            </form>
            <div id="perform_crm/addcalllog_Results"></div>
            <hr/>
            <div class="accordion">
                {$logs}
            </div>
        </body>
</html>