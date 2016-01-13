<div style="display:inline-block; width:70%;">
    <form name="sections_{$tabnum}_managesystem/managewindows_Form" id="sections_{$tabnum}_managesystem/managewindows_Form" action="#" method="post">
        <table>
            <tr>
            <input type="hidden"  name='section[{$section['inputChecksum']}][inputChecksum]' value="{$section['inputChecksum']}">
            <input type="hidden" id="windowid"  name='section[{$section['inputChecksum']}][swid]' value="{$windowid}">
            <input type="hidden" name='section[{$section['inputChecksum']}][swstid]' value="{$tabnum}">
            <td>{$lang->sectionname}</td><td><input type="text" name='section[{$section['inputChecksum']}][name]' value="{$section['name']}"></td>
            <td>{$lang->sectiontable}</td><td>{$section_tables_selectlist}</td>
            <td>{$lang->sequence}</td><td><input type="number" name='section[{$section['inputChecksum']}][sequence]' value="{$section['sequence']}"></td>
            <td>{$lang->ismain}</td><td><input type="checkbox" name='section[{$section['inputChecksum']}][isMain]' {$section_ismain_check} value="1"></td>

            </tr>
            <tr>
                <td>{$lang->description}</td><td><input type="text" name='section[{$section['inputChecksum']}][description]' value="{$section['description']}"></td>
                <td>{$lang->sectiontype}</td><td>{$section_type_selectlist}</td>
                <td>{$lang->sectiondisplaytype}</td><td>{$section_displaytype_selectlist}</td>
            </tr>
            <tr>
                <td>{$lang->helpcomments}</td><td><textarea name='section[{$section['inputChecksum']}][comment]'>{$section['comment']}</textarea></td>
                <td>{$lang->displaylogic}</td><td><textarea name='section[{$section['inputChecksum']}][displayLogic]'>{$section['displayLogic']}</textarea></td>
                <td>{$lang->sqlwhereclause}</td><td><textarea name='section[{$section['inputChecksum']}][sqlWhereClause]'>{$section['sqlWhereClause']}</textarea></td>

            </tr>
            <tr>
                <td>{$lang->custsavename}</td><td><input type="text" name='section[{$section['inputChecksum']}][saveModuleName]' value="{$section['saveModuleName']}"></td>
            </tr>
            <tr><td>{$lang->isactive}</td><td><input type="checkbox" {$section_isactive_check} name='section[{$section['inputChecksum']}][isActive]' value="1"></td>
            </tr>
            <tr>
            <input type='submit' style="cursor: pointer" class='button' form="sections_{$tabnum}_managesystem/managewindows_Form" value="{$lang->savesection}" id="sections_{$tabnum}_managesystem/managewindows_Button">
            </tr>
        </table>
    </form>
    <div id="sections_{$tabnum}_managesystem/managewindows_Results"></div>
    {$section_table_fields}
</div>