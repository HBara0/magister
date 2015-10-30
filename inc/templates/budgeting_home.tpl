<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->budgetingoverview}</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
        {$headerinc}
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>

    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h1>{$lang->budgetingoverview}</h1>
            <form name="perform_budgeting/budgethome_Form" id="perform_budgeting/budgethome_Form" action="#" method="post">
                <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                    <table class = "datatable" width = "100%">
                        <thead>
                            <tr>
                                <th width = "100%"><input type = "checkbox" id = 'affiliatefilter_checkall'><input class = 'inlinefilterfield' type = 'text' tabindex = "2" placeholder = "{$lang->search} {$lang->affiliate}" style = "display:inline-block;width:70%;margin-left:5px;"/></th>
                            </tr>
                        </thead>
                        <tbody>
                            {$affiliates_list}
                        </tbody>
                    </table>
                </div>
                <input type="hidden" value="generate" name="action">
                <input type="submit" id="perform_budgeting/budgethome_Button" value="{$lang->generate}" class="button" />
                <hr>
                <div style="display: inline-block;width:50%; float:left" id="perform_budgeting/budgethome_Results">
                    {$checklist}
                </div>

            </form>
        </div>
    </td>
</tr>
{$footer}
</body>
</html>