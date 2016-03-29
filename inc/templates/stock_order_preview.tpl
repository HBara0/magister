<h1>{$lang->preview}</h1>
<h1>{$lang->internalform} - {$lang->stockorder} - {$affiliate}</h1>
{$stockorder}
<br />
<form id="add_stock/order_Form"  name="add_stock/order_Form" method="post" >
    <input type="hidden" name="identifier" value="{$core->input[identifier]}">
    <div align="center"><input type="button" value="{$lang->prev}" class="button" onClick="goToURL('index.php?module=stock/stockorder&identifier={$core->input[identifier]}');"> <input type="button" id="add_stock/order_Button" name="add_stock/order_Button" value="{$lang->save}" class="button"></div>
</form>

<div id="add_stock/stockorder_Results"></div>
