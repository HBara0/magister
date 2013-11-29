<div id="popup_sharemeeting" title="{$lang->sharemeeting}">
    <form name="perform_meetings/list_Form" id="perform_meetings/list_Form"  method="post">
        <input type="hidden" name="mtid" id="mtid" value="{$mtid}" />
        <input type="hidden" value="do_share" name="action" id="action" />

        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td class="altrow2" colspan="2">{$lang->sharewith}</td>
            </tr>
            <tr id="sharewith_row">
                <td colspan="2">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody id="invitationsgroup_tbody">
                            <tr id="1">
                                <td colspan="2">  
                                    <div style="width:100% ;height:250px; overflow:auto; display:inline-block; vertical-align:top;">
                                        <table class="datatable" width="100%">

                                            {$sharewith_rows} 
                                        </table>
                                    </div> 
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </td>

            </tr>
        </table>


        <div style="display:table; border-collapse:collapse; width:100%;">

            <div style="display:table-row;">
                <div style="display: table-cell; width:35%;">
                    <input type="submit" class="button" value="{$lang->savecaps}" id="perform_meetings/list_Button" />
                    <input type="reset" class="button"   onclick="$('#popup_sharemeeting').dialog('close')"value="{$lang->close}"/>
                </div>
            </div>

            <div style="display:table-row;width:25%;">
                <div style="display:table-cell;width:25%;" id="perform_meetings/list_Results"></div>
            </div>
        </div>

    </form>
</div>