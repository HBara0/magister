<h1>{$title}</h1>
<form name="perform_reputation/{$actiontype}_Form" id="perform_reputation/{$actiontype}_Form" method="post">
    <input type="hidden" id="action" name="action" value="{$action}" />
    <table>
        <tr>
            <td>{$lang->title}</td>
            <td><input name="title" id="title" type="text" maxlength="200" value="{$reputation[title]}" tabindex="1" autocomplete="off"/></td>
        </tr>
        <tr>
            <td>{$lang->description}</td><td><textarea cols="30" rows="5" id="description" name="description" tabindex="2" value="{$reputation[description]}" ></textarea></td>
        </tr>
        <tr>
            <td>{$lang->url}</td><td><input type="text" name="url" value="{$reputation[url]}" tabindex="3" /></td>
        </tr>
        <tr>
            <td>
                <input type='button' class='button' value="{$lang->savecaps}" id='perform_reputation/{$actiontype}_Button'>
            </td>
        </tr>
    </table>
</form>
<div id="perform_reputation/{$actiontype}_Results"></div>