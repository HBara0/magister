<h1>{$lang->listavailablegenerics}</h1>
<table class="datatable">
    <thead>
        <tr>
            <th>{$lang->id}</th><th>{$lang->title}</th><th>{$lang->segment}</th>
        </tr>
    </thead>
    <tbody>
        {$generics_list}
    </tbody>
</table>
<hr />
<h1>{$lang->addagenericproduct}</h1>
<form id="add_products/generics_Form" name="add_products/generics_Form" action="#" method="post">
    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td width="10%">{$lang->title}</td>
            <td width="90%"><input type="text" id="title" name="title" /></td>
        </tr>
        <tr>
            <td width="10%">{$lang->segment}</td>
            <td width="90%">{$segments_list}</td>
        </tr>
        <tr>
            <td>{$lang->description}</td><td><textarea cols="30" rows="5" id="description" name="description"></textarea></td>
        </tr>
        <tr>
            <td colspan="2"><input type="button" id="add_products/generics_Button" value="{$lang->add}" /><input type="reset" value="{$lang->reset}" />
                <div id="add_products/generics_Results"></div>
            </td>
        </tr>
    </table>
</form>