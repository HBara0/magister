<h1>{$matchinproducts}</h1>

<form name="perform_sourcing/matchproducts_Form" action="{$_SERVER[QUERY_STRING]}" method="post"  id="perform_sourcing/matchproducts_Form">
    <input class="rounded" type="hidden" value="do_match" name="action" id="action" />
    <div align="center">
        <table width="100%" class="datatable">
            <thead>
            <th width="40%"> <h1>Matchitem</h1>{$lang->matchitem}</th><th>&nbsp;</th><th width="40%"><h1>Matchwith</h1>{$lang->matchwith}</th>

            </thead>
            <tbody>
                {$matchproduct}
            </tbody>
        </table>

    </div>

    <input type="button" class="button" value="import" id="perform_sourcing/matchproducts_Button" />
    <div style="display:table-row">
        <div style="display:table-cell;"id="perform_sourcing/matchproducts_Results"></div>
    </div>
</form>