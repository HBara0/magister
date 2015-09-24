<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->facilitymgmt}</title>
        {$headerinc}
    </head>
    <body>
    <tr>
        {$header}
        {$menu}
        <td class="contentContainer" colspan="2">
            <h1>{$lang->facilitymgmt}</h1>
            <form name="perform_facilitymgmt/managefacility_Form" id="perform_facilitymgmt/managefacility_Form"  action="#" method="post">
                <input type="hidden" id="" name="facility[fmfid]" value="{$facility[fmfid]}">
                <table class="datatable" width="100%" border="0" cellspacing="0" cellpadding="2">
                    <tbody>
                        <tr><td>{$lang->name}</td> <td><input type="text" name="facility[name]" value="{$facility[name]}"/></td></tr>
                        <tr><td>{$lang->aff}</td><td>{$affiliate_list}</td></tr>
                        <tr><td>{$lang->type}</td><td>{$factypes_list}</td></tr>
                        <tr><td>{$lang->within}</td><td>{$facilities_list}</td></tr>
                        <tr><td>{$lang->capacity}</td>
                            <td><input type="number" name="facility[capacity]" value="{$facility[capacity]}"/></td>
                        </tr>
                        <tr><td>{$lang->numoccupants}</td>
                            <td><input type="number" name="facility[numOccupants]" value="{$facility[numOccupants]}"/></td>
                        </tr>
                        <tr><td>{$lang->dimensions}</td>
                            <td><input type="number" name="facility[dimensions][x]" value="{$facility[x]}" placeholder="x"/>
                                <input type="number" name="facility[dimensions][y]" value="{$facility[y]}" placeholder="y"/>
                                <input type="number" name="facility[dimensions][z]" value="{$facility[z]}" placeholder="z"/>
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
        </td>
    </tr>
    {$footer}
</body>
</html>