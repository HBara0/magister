<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$supplier[maindetails][companyName]}</title>
        {$headerinc}
        <link href="{$core->settings[rootdir]}/css/rateit.min.css" rel="stylesheet" type="text/css" />
        <link href="{$core->settings[rootdir]}/css/sourcing_supplierprofile.css" rel="stylesheet" type="text/css" />
        <link href="{$core->settings[rootdir]}/css/rml.min.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            .blur {
                color: transparent;
                text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
            }
        </style>
        <script src="{$core->settings[rootdir]}/js/jquery.rateit.min.js" type="text/javascript"></script>
        <script>
            $(function() {
                var tooltipvalues = ["{$lang->verylowopp}", "{$lang->lowopp}", "{$lang->mediumopp}", "{$lang->highopp}", "{$lang->veryhighopp}"];
                $(document).on("over", "#ratingdiv", function(event, value) {
                    $(this).attr("title", tooltipvalues[value - 1]);
                });
            {$header_blurjs}

                $("input[type='radio'][id=approved_type]").attr('disabled', true);
                $(document).on('change', ".priceok", function() {
                    var val = $(this).val();
                    /* find the first checkbox in the next parent div after each input with class approved*/
                    var obj = $(this).parent().parent().nextAll().has(":checkbox").first().find(":checkbox").removeAttr("disabled").prop("checked", true);
                    $("div[id^='" + obj.val() + "']").show(); /* obj.val() Get the value of the checkbox in the next div (that has calss main) */
                    $(".stageapproved,.stagenotapproved,.notapplocable").removeAttr("disabled");
                });
                $(document).on('change', ".pricenotOk", function() {
                    $(".stageapproved,.stagenotapproved,.notapplocable").attr('disabled', true);
                });
                $(document).on('change', ".stageapproved,.notapplocable", function() {
                    $(this).parent().parent().parent().next().show().find("textarea,:text").removeAttr("disabled");
                    /* find the first checkbox in the next parent div after each radio checked with class stageapproved after the main Div*/
                    var obj = $(this).parent().parent().parent().nextAll().has(":checkbox").first().find(":checkbox").removeAttr("disabled").prop("checked", true);
                    var nextdiv = $("div[id^='" + obj.val() + "']");
                    $("div[id^='sourcingnotpossible_body']").hide()
                    $("div[id^='manuallyselect_entity']").show()
                    if(nextdiv.length) {
                        //	$("html, body").animate({ scrollTop: $('#'+nextdiv.attr('id')).offset().top }, 1000)
                        //$("html, body").scrollTo ('#'+nextdiv.attr('id'));  /* scrolling to a specified next div.*/
                    }
                    ;
                    $("div[id^='" + obj.val() + "']").show(); /* obj.val() Get the value of the checkbox in the next div (that has calss main) */

                });
                $(document).on('change', ".stagenotapproved,.pricenotOk", function() {
                    /*disable and rehide subsequent stage textarea and text*/
                    $(this).parent().parent().parent().next().first().not("div[id^='results']").hide().find("textarea,:text").attr("disabled", true);
                    $(this).parent().parent().parent().nextAll().has(":checkbox").first().find(":checkbox").attr('disabled', true);
                    $("html, body").animate({scrollTop: $(document).height() - 450}, 1000); /*scroll down to the end of body */
                    $("div[id^='sourcingnotpossible_body']").show().find("textarea").focus();
                });
                /*expand/collapse report section START*/

                $(document).on('change', "input[type='checkbox'][id$='_check']", function() {
                    var id = $(this).attr("id");
                    $("div[id^='" + $(this).val() + "']").slideToggle("slow");
                });
            {$hide_productsection}

                $("span[id^='contactpersondata_']").each(function() {

                    var id = $(this).attr('id').split('_');
                    $(this).qtip({
                        content: {
                            text: '<img class="throbber" src="images/loading.gif" alt="Loading..." />',
                            ajax: {
                                url: 'index.php?module=sourcing/supplierprofile&action=preview&sid=' + id[2] + '&rpid=' + id[1],
                                data: {}, // Data to pass along with your request
                                success: function(data, returnedData) {
                                    this.set('content.text', data);
                                }
                            },
                            title: {
                                text: 'Contact details',
                                button: true
                            }
                        },
                        position: {
                            viewport: $(window),
                        },
                        show: {
                            event: 'mouseover',
                            solo: true
                        },
                        hide: 'unfocus',
                        style: {
                            classes: ' ui-tooltip-light ui-tooltip-shadow'
                        }
                    });
                });
            });
        </script>
    </head>
    <body>
        {$header}
    <tr> {$menu}
        <td class="contentContainer">
            <div style="margin-bottom: 10px;">
                <h3 style="margin-bottom: 5px;">{$supplier[maindetails][companyName]} {$supplier[maindetails][businessPotential_output]}</h3>

                {$supplier[maindetails][relationMaturity_output]}
                {$supplier[relatedsupplier_output]}
            </div>
            <div style='display:inline-block; width:50%; padding:5px; vertical-align:top;'>
                <div class="subtitle border_right"><strong>{$lang->contactdtails}</strong></div>
                <div class="border_right">{$lang->fulladress}: <span class="contactsvalue">{$supplier[contactdetails][fulladress]}</span><br />
                    {$lang->pobox}: <span class="contactsvalue">{$supplier[contactdetails][poBox]}</span><br />
                    {$lang->telephone}: <span class="contactsvalue">{$supplier[contactdetails][phones]}</span><br />
                    {$lang->fax}: <span class="contactsvalue">{$supplier[contactdetails][fax]}</span><br />
                    {$lang->email}: <span class="contactsvalue"> <a href="mailto:{$supplier[contactdetails][mainEmail]}">{$supplier[contactdetails][mainEmail]}</a></span><br />
                    {$lang->website}: <span class="contactsvalue"><a href="{$supplier[contactdetails][website]}"  target="_blank">{$supplier[contactdetails][website]}</a></span><br />
                </div>
                <div class="border_right">{$contactsupplier_button}</div>
            </div>
            <div style='display:inline-block; width:45%; padding:5px; vertical-align:top;'>sss
                <div class="subtitle"><strong>{$lang->contactperson}</strong></div>
                {$contactpersons_output}</div>
            <div style='display:inline-block; width:50%; padding:5px; margin-top:10px; vertical-align:top;' class="border_right"><strong>{$lang->segments}</strong><br />
                {$segments_output}</div>
            <div style='display:inline-block; width:45%; padding:5px; margin-top:10px; vertical-align:top;'><strong>{$lang->activityarea}</strong><br />
                {$activityarea_output}</div>
            <div style="width:100%; max-height: 200px; overflow:auto; display:inline-block; vertical-align:top; margin-top: 10px;">
                <table class="datatable" width="100%" style="table-layout:fixed;">
                    <thead>
                        <tr>
                            <td class="thead">{$lang->casnum}</td>
                            <td class="thead">{$lang->checmicalproduct}</td>
                            <td class="thead">{$lang->supplytype}</td>
                            <td class="thead">{$lang->synonyms}</td>
                        </tr>
                    </thead>
                    {$chemicalslist_section}
                </table>
                <hr />
            </div>
            <div style="display:inline-block; overflow:auto; width:100%; max-height:200px; vertical-align:top; margin-top:10px;">
                <table class="datatable" width="100%">
                    <thead>
                        <tr>
                            <td class="thead">{$lang->genericproducts}</td>
                        </tr>
                    </thead>
                    {$genericproductlist_section}
                </table>
                <hr />
            </div>
            <div>
                <div class="subtitle" style="margin-top: 10px;">{$lang->comments}</div>
                <div style='padding:5px; width:100%;' class='border_bottom'><strong>{$lang->cobriefing}</strong><br />
                    <p style="width:1000px; word-wrap: break-word;">{$supplier[maindetails][coBriefing]}</p></div>
                <div style='padding:5px; width:100%;' class='border_bottom'><strong>{$lang->historical}</strong><br />
                    <p style="width:1000px; word-wrap: break-word;">{$supplier[maindetails][historical]}</p></div>
                <div style='display:inline-block; width:45%; padding:5px; vertical-align:top;' class='border_bottom border_right'><strong>{$lang->marketingrecords}</strong><br />
                    <p style="width:1000px; word-wrap: break-word;">{$supplier[maindetails][marketingRecords]}</p></div>
                <div style='display:inline-block; width:45%; padding:5px;' class='border_bottom'><strong>{$lang->sourcingrecords}</strong><br />
                    <p style="width:1000px; word-wrap: break-word;">{$supplier[maindetails][sourcingRecords]}</p></div>
                <div style='padding:5px;' class='border_bottom'><strong>{$lang->commentstoshare}</strong><br />
                    <p style="width:1000px; word-wrap: break-word;">{$supplier[maindetails][commentsToShare]}</p></div>
                    {$blacklist_histories}
                <div>
                    <hr />
                    <div class="subtitle">{$lang->contacthistory}</div>
                    {$contacthistory_section}
                    <br />
                    <hr />
                    {$reportcommunication_section} </div>
            </div></td>
    </tr>
    {$footer}
</body>
</html>