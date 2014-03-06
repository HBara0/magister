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
            <form name="perform_development/createrequirement_Form" method="post" id="perform_development/createrequirement_Form" >
                <input type="hidden" value="do_add" name="action" id="action" />
                <div style="display:block; padding:5px;" >
                    <div style="display:inline-block;width:10%">Module</div>
                    <div style="display:inline-block;width:55%"><input type="text" size="50" name="development[modulefield]"  required="required"/>  </div>
                </div>
                <div style="display:block; padding:5px;" >
                    <div style="display:inline-block;width:10%">Title</div>
                    <div style="display:inline-block;width:55%"><input type="text" size="50" name="development[title]" required="required"/>  </div>
                </div>

                <div style="display:block; padding:5px;" >
                    <div style="display:inline-block;width:10%">Ref Word</div>
                    <div style="display:inline-block;width:55%"><input type="text" name="development[refWord]" /></div>
                </div>

                <div style="display:block; padding:5px;" >
                    <div style="display:inline-block;width:10%">Parent</div>
                    <div style="display:inline-block;width:55%">{$parent_list}</div>
                </div>
                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%; vertical-align: top;">Description</div>
                    <div style="display:inline-block;width:70%"><textarea class="texteditor" name="development[description]" cols="90" rows="25"></textarea>    </div>
                </div>

                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%;vertical-align: top;">userInterface </div>
                    <div style="display:inline-block;width:70%"><textarea cols="90" rows="25" class="texteditor" name="development[userInterface]"></textarea> </div>
                </div>
                <div style="display:block; padding:5px;">
                    <div style="display:inline-block;width:10%;vertical-align: top;">Security</div>
                    <div style="display:inline-block;width:70%"><textarea cols="90" rows="25" class="texteditor" name="development[security]"></textarea>   </div>
                </div>
                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%;vertical-align: top;">Performance</div>
                    <div style="display:inline-block;width:70%"><textarea cols="90" rows="25"class="texteditor" name="development[performance]"></textarea>  </div>
                </div>
                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%">Is Approved </div>
                    <div style="display:inline-block;width:55%"><input type="checkbox" value="1" name="development[isApproved]" /></div>
                </div>
                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%">Is Completed</div>
                    <div style="display:inline-block;width:55%"><input type="checkbox" value="1" name="development[isCompleted]" /></checkbox> </div>
                </div>
                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%">{$lang->requestedby}</div>
                    <div style="display:inline-block;width:55%"><select name=development[requestedby]>   {$requestedby_list} </select>    </div>
                </div>
                <div style="display:block; padding:5px">
                    <div style="display:inline-block;width:10%">Assigned To</div>
                    <div style="display:inline-block;width:55%"><select name=development[assignedTo]> >{$assignedto_list} </select>   </div>
                </div>
                <div style="display:block; padding:5px">
                    <div style="display:inline-block;"><input type="submit" class="button" value="save{$lang->save}" id="perform_development/createrequirement_Button" /> </div>
                    <div style="display:inline-block;"><input type="reset" class="button" value="{$lang->reset}"/>   </div>
                </div>
                <div id="perform_development/createrequirement_Results" style="display:block; padding:5px">
                </div>

            </form>
        </td>
    </tr>
    {$footer}
</body>
</html>