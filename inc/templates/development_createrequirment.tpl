<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$requirement[title]}</title>
        {$headerinc}

        <script type="text/javascript">
            $(document).ready(function() {
                $('.texteditor').redactor({
                    buttons: ['html', '|', 'formatting', '|', 'bold', 'italic', 'deleted', '|',
                        'unorderedlist', 'orderedlist', 'outdent', 'indent', '|',
                        'table', '|', 'alignment', '|', 'horizontalrule'],
                    fullpage: true
                });
            });
        </script>
    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <form name="perform_development/createrequirment_Form" method="post" id="perform_development/createrequirment_Form" >
                <input type="hidden" value="do_add" name="action" id="action" />

                <div style="display:block; padding:5px;" >
                    <div style="display:inline-block;width:10%"> module </div>

                    <div style="display:inline-block;width:55%"> <input type="text" size="50" name="development[modulefield]" />  </div>
                </div>

                <div style="display:block; padding:5px;" >
                    <div style="display:inline-block;width:10%"> title </div>
                    <div style="display:inline-block;width:55%"> <input type="text"  size="50" name="development[title]" />  </div>
                </div>

                <div style="display:block; padding:5px;" >
                    <div style="display:inline-block;width:10%"> refKey </div>
                    <div style="display:inline-block;width:55%">   <input type="text" name="development[refKey]" />  </div>
                </div>

                <div style="display:block; padding:5px;" >
                    <div style="display:inline-block;width:10%"> parent </div>
                    <div style="display:inline-block;width:55%"> <select name="development[parent]">{$parent_list}</select> </div>
                </div>
                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%; vertical-align: top;">dessssssc </div>

                    <div style="display:inline-block;width:70%"> <textarea class="texteditor"   name="development[description]" cols="90" rows="25"></textarea>    </div>
                </div>


                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%;vertical-align: top;">userInterface </div>
                    <div style="display:inline-block;width:70%"> <textarea  cols="90" rows="25" class="texteditor" name="development[userInterface]"></textarea> </div>
                </div>
                <div style="display:block; padding:5px;vertical-align: top;">
                    <div style="display:inline-block;width:10%">security </div>
                    <div style="display:inline-block;width:55%">   <input type="text" size="30" name="development[security]" />  </div>
                </div>
                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%;vertical-align: top;">performance </div>
                    <div style="display:inline-block;width:70%">  <textarea  cols="90" rows="25"class="texteditor" name="development[performance]"></textarea>  </div>
                </div>
                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%">isApproved </div>
                    <div style="display:inline-block;width:55%"> <input type="checkbox" value="1" name="development[isApproved]" /> </div>
                </div>
                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%">isCompleted </div>
                    <div style="display:inline-block;width:55%"> <input type="checkbox" value="1" name="development[isCompleted]" /></checkbox> </div>
                </div>
                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%">requestedby </div>
                    <div style="display:inline-block;width:55%"><select name=development[requestedby]>   {$requestedby_list} </select>    </div>
                </div>

                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%">assignedTo </div>
                    <div style="display:inline-block;width:55%"> <select name=development[assignedTo]> >{$assignedto_list} </select>   </div>
                </div>
                <div style="display:block; padding:5px">
                    <div style="display:inline-block;"> <input type="button" class="button" value="save{$lang->save}" id="perform_development/createrequirment_Button" /> </div>
                    <div style="display:inline-block;" > <input type="reset" class="button" value="{$lang->reset}"/>   </div>
                </div>
                <div id="perform_development/createrequirment_Results" style="display:block; padding:5px">

                </div>

            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>