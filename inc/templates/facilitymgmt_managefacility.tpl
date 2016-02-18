<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->facilitymgmt}</title>
        {$headerinc}
        <script>
            $(function() {
                $(document).on('change', 'select[id="typelist"]', function() {
                    if($(this).children(":selected").attr("id") == 'isMain') {
                        $('td[id="within"]').each(function(i, obj) {
                            $(obj).hide();
                        });
                    }
                    else {
                        $('td[id="within"]').each(function(i, obj) {
                            $(obj).show();
                        });
                    }
                });
            });
        </script>
    </head>
    <body>
        {$header2}
        <div class="container" style="padding-top: 70px">
            <h1>{$lang->facilitymgmt}</h1>
            <form name="perform_facilitymgmt/managefacility_Form" id="perform_facilitymgmt/managefacility_Form"  action="#" method="post">
                <input type="hidden" id="" name="facility[fmfid]" value="{$facility[fmfid]}">
                <table class="datatable" width="100%" border="0" cellspacing="0" cellpadding="2">
                    <tbody>
                        <tr><td>{$lang->name}</td> <td><input type="text" name="facility[name]" value="{$facility[name]}"/></td></tr>
                        <tr><td>{$lang->aff}</td><td>{$affiliate_list}</td></tr>
                        <tr><td>{$lang->type}</td><td>{$factypes_list}</td></tr>
                        <tr><td id="within" {$show_within}>{$lang->within}</td><td id="within" {$show_within}>{$facilities_list}</td></tr>
                        <tr><td>{$lang->capacity}</td>
                            <td><input type="number" name="facility[capacity]" value="{$facility[capacity]}"/></td>
                        </tr>
                        <tr><td>{$lang->numoccupants}</td>
                            <td><input type="number" name="facility[numOccupants]" value="{$facility[numOccupants]}"/></td>
                        </tr>
                        <tr><td>{$lang->widthinmeters}</td>
                            <td>
                                <input type="number" name="facility[dimensions][x]" value="{$facility[x]}" />
                            </td>
                        </tr>
                        <tr><td>{$lang->lengthinmeters}</td>
                            <td>
                                <input type="number" name="facility[dimensions][y]" value="{$facility[y]}" />
                            </td>
                        </tr>
                        <tr><td>{$lang->heightinmeters}</td>
                            <td>
                                <input type="number" name="facility[dimensions][z]" value="{$facility[z]}"/>
                            </td>
                        </tr>
                        <tr><td>{$lang->image}</td>
                            <td>
                            </td>
                        </tr>
                        <tr><td>{$lang->color}</td>
                            <td><input type="color" name="facility[idColor]" value="{$facility[idColor]}"/></td>
                        </tr>
                        <tr><td>{$lang->isactive}</td>
                            <td><input type="checkbox" name="facility[isActive]" value="1" {$checked[isActive]}/></td>
                        </tr>
                        <tr><td>{$lang->allowreservation}</td>
                            <td><input type="checkbox" name="facility[allowReservation]" value="1" {$checked[allowReservation]}/></td>
                        </tr>
                    </tbody>
                </table>
                <br/>
                <input type="submit" id="perform_facilitymgmt/managefacility_Button" value="Save" class="button"/>
            </form>
            <div id="perform_facilitymgmt/managefacility_Results"></div>
        </div>
        {$footer2}
        {$rightsidemenu}
    </body>
</html>