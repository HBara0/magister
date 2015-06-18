<div id="popup_applicationdescription" title="{$lang->description}">
    <form action="#" method="post" id="perform_products/functions_Form" name="perform_products/functions_Form">
        <input type="hidden" name="action" value="save_descr" />
        <input type='hidden' name='segfuncapp' value='{$safid}'/>
        <div style="display:block">
            <table>
                <tr>
                    <td>{$lang->publishonweborunpub}</td>
                    <td>{$publishonwebsite}</td>
                </tr>
            </table>
        </div>
        <br>
        <textarea rows="5" cols="60" class="basictxteditadv" name="segapdescription" id="segapdescription{$safid}">{$segapdescriptions}</textarea>
        <hr />
        <input type='button' id='perform_products/functions_Button' value='{$lang->savecaps}' class='button'/>
        <div id="perform_products/functions_Results"></div>
    </form>
</div>