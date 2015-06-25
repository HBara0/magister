<div id="popup_editcountry" title="{$lang->editaff}">
    <form id="perform_regions/affiliates_Form" name="perform_regions/affiliates_Form" action="#" method="post">
        <input type="hidden" name="action" value="perform_editaffiliate" />
        <input type="hidden" name="id" value="{$id}" />
        <h2>{$lang->chartspec}</h2>
        <table>
            <tr>
                <td>{$lang->height}</td> <td><input type="number" name="spec[chartspec][height]" value="{$chartspec['chartspec']['height']}"></td>
            </tr>
            <tr>
                <td>{$lang->width}</td> <td><input type="number" name="spec[chartspec][width]" value="{$chartspec['chartspec']['width']}"></td>
            </tr>
            <tr>
                <td>{$lang->cssclassname}</td> <td><input type="text" name="spec[class]" value="{$chartspec['class']}"></td>
            </tr>
        </table>
        <div align="center"><input type='button' id='perform_regions/affiliates_Button' value='{$lang->save}' class='button'/></div>
    </form>
    <div id="perform_regions/affiliates_Results"></div>
</div>