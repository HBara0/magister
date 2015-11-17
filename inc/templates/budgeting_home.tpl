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
                <div id="filters" style="display:none;">
                    <div style="width:100%; height:150px; overflow:auto;  vertical-align:top; margin-bottom: 10px;">
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
                </div>
                <div class="tablefilters_row_toggle"  onClick="$('#filters').toggle();" style=" padding: 0px !important; text-align: center !important; height: 2px !important;border-bottom: 1px solid #CCC !important;color: #666; line-height: 6px;cursor: pointer; width: 100%;margin:auto;text-align: center">
                    <a style="float:left;text-align: center" ><img src="{$core->settings['rootdir']}/images/icons/search.gif">{$lang->search}</a>
                </div>
                <br>
                <hr>
                <div style="display: inline-block;width:100%; margin:auto " id="perform_budgeting/budgethome_Results">
                    {$checklist}
                </div>

            </form>
        </div>
    </td>
</tr>
{$footer}
</body>
</html>