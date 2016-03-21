<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->copyassignments}</title>
        {$headerinc}
        <script>
            $(document).ready(function() {
                $("input[name='assignmentbasis']").change(function() {
                    if($("input[id='assign_user_based_radiobtn']").is(':checked')) {
                        $("div[id='assign_user_based']").effect("highlight", {color: "#D6EAAC"}, 1500).show();
                        //hide and disable aff/segment assignment section
                        $("div[id='assign_aff_based']").find("div[class='col-xs-12 col-sm-10 col-md-10']").each(function() {
                            $(this).children().attr('disabled', 'disabled');
                        });
                        $("div[id='assign_user_based']").find("div[class='col-xs-12 col-sm-10 col-md-10']").each(function() {
                            $(this).children().removeAttr('disabled');
                        });
                        $("div[id='assign_aff_based']").hide();
                        //select default entity type and assignment type
                        $("input[value='s']").prop('checked', false);
                        $("input[name='transfer[assignment]']").prop('checked', false);
                    }
                    else if($("input[id='assign_aff_based_radiobtn']").is(':checked')) {
                        //hide and disable user copy assignment section
                        $("div[id='assign_user_based']").find("div[class='col-xs-12 col-sm-10 col-md-10']").each(function() {
                            $(this).children().attr('disabled', 'disabled');
                        });
                        $("div[id='assign_aff_based']").find("div[class='col-xs-12 col-sm-10 col-md-10']").each(function() {
                            $(this).children().removeAttr('disabled');
                        });
                        $("div[id='assign_user_based']").hide();
                        $("div[id='assign_aff_based']").effect("highlight", {color: "#D6EAAC"}, 1500).show();
                        $("input[value='s']").prop('checked', true);
                        $("input[name='transfer[assignment]']").prop('checked', true);
                    }
                });
            });
        </script>
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->copyassignments}</h1>
            <form action="#" method="post" id="perform_users/copyassignments_Form" name="perform_users/copyassignments_Form" class="form-horizontalm">
                <div class="panel panel-default" style="padding:10px;width:70%"><label>Assignment Type:&nbsp;&nbsp;</label><br/>
                    <input type="radio" name="assignmentbasis" id="assign_user_based_radiobtn" checked="checked"> Copy User Assignment<br/>
                    <input type="radio" name="assignmentbasis" id="assign_aff_based_radiobtn">Assign based on Affiliates / Segemnts
                </div>

                <div id="assign_user_based" style="padding:10px;width:70%" class="row">
                    <div class="form-group">
                        <label for="fromUser" class="col-xs-12 col-sm-2 col-md-2 control-label"> From User:</label>
                        <div class="col-xs-12 col-sm-10 col-md-10">
                            {$fromuser_selectlist}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="toUser" class="col-xs-12 col-sm-2 col-md-2 control-label"> To User:</label>
                        <div class="col-xs-12 col-sm-10 col-md-10">
                            {$touser_selectlist}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="affiliate" class="col-xs-12 col-sm-2 col-md-2 control-label"> Affiliate:</label>
                        <div class="col-xs-12 col-sm-10 col-md-10">
                            {$affiliate_selectlist}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="segment" class="col-xs-12 col-sm-2 col-md-2 control-label">  Segments:</label>
                        <div class="col-xs-12 col-sm-10 col-md-10">
                            {$segments_selectlist}
                        </div>
                    </div>
                </div>
                <br/>
                <!--
                //offer a way to assign an employee to specific suppliers based on the supplier's segments and affiliates.-->
                <div id="assign_aff_based" style="display:none;padding:10px;width:70%" class="row">
                    <div class="form-group">
                        <label for="fromUser" class="col-xs-12 col-sm-2 col-md-2 control-label"> User:</label>
                        <div class="col-xs-12 col-sm-10 col-md-10">
                            {$user_selectlist}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fromUser" class="col-xs-12 col-sm-2 col-md-2 control-label"> Affiliate:</label>
                        <div class="col-xs-12 col-sm-10 col-md-10">
                            {$affiliate_selectlist}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fromUser" class="col-xs-12 col-sm-2 col-md-2 control-label">Segment:</label>
                        <div class="col-xs-12 col-sm-10 col-md-10">
                            {$segments_selectlist}
                        </div>
                    </div>
                </div>

                <div class="row">
                    Assignments <input type="checkbox" name="transfer[assignment]" value="1">&nbsp;&nbsp;
                    User Transfer Assignments <input type="checkbox" name="transfer[userassignments]" value="1"><br/>
                    Customers <input type="checkbox" name="types[]" value="c">&nbsp;&nbsp;
                    Suppliers <input type="checkbox" name="types[]" value="s"><br/>
                </div>
                <div class="row">
                    <input type="button" value="{$lang->savecaps}" id="perform_users/copyassignments_Button" tabindex="26" class="btn btn-success"/>
                </div>
                <div id="perform_users/copyassignments_Results" class="row"></div>
            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>