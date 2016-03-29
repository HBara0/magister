<h1>{$lang->generatesalesreport}</h1>
<div style="margin-left: 5px;">
    <form name="do_crm/salesreport_Form" id="do_crm/salesreport_Form" method="post" action="index.php?module=crm/salesreport&amp;action=do_generatereport">
        {$lang->type} <select name="type" id="type">
            <option value="detailed">{$lang->detailed}</option>
            <option value="topcustomers">{$lang->topcustomers}</option>
            <option value="topemployees">{$lang->topemployees}</option>
            <option value="topsuppliers">{$lang->topsuppliers}</option>
            <option value="topproducts">{$lang->topproducts}</option>
        </select>

        <fieldset style="vertical-align:top; margin-top:10px; margin-bottom:10px;" class="altrow2">
            <legend>{$lang->filters}</legend>
            <div style="display:inline-block; width:45%; vertical-align:top;">{$lang->affiliate}<br />{$affiliates_list}</div>
            <div style="display:inline-block; width:45%; vertical-align:top;">{$lang->saletype}<br />{$saletypes_list}</div>
            <div style="display:inline-block; width:45%; vertical-align:top;">{$lang->supplier}<br />{$suppliers_list}</div>
            <div style="display:inline-block; width:45%; vertical-align:top;">{$lang->products}<br />{$products_list}</div>
            <div style="display:inline-block; width:45%; vertical-align:top;">{$lang->customer}<br />{$customers_list}</div>
        </fieldset>
        <div>
            <div style="display:inline-block; width:10%; vertical-align:top;">{$lang->fromdate}</div><div style="display:inline-block; width:20%; vertical-align:top;"><input type="text" id="pickDate_from" autocomplete="off" tabindex="1" /> <input type="hidden" name="fromDate" id="altpickDate_from" /></div>
            <div style="display:inline-block; width:10%; vertical-align:top;">{$lang->todate}</div><div style="display:inline-block; width:20%; vertical-align:top;"><input type="text" id="pickDate_to" autocomplete="off" tabindex="2" /> <input type="hidden" name="toDate" id="altpickDate_to" /></div>
            <div style="display:inline-block; width:10%; vertical-align:top;">{$lang->fxtype}</div><div style="display:inline-block; width:20%; vertical-align:top;">{$fxtypes_selectlist}</div>
        </div>
        <hr />
        <input type="submit" id="do_crm/salesreport_Button" value="{$lang->generatereport}" class="button"> <input type="reset" value="{$lang->reset}" class="button">
    </form>
</div>