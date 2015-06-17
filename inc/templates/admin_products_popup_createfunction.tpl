<div id="popup_cretefunction" title="{$lang->create}">
    <form action="#" method="post" id="perform_products/functions_Form" name="perform_products/functions_Form">
        <input type="hidden" name="action" value="do_create" />
        <input type="hidden" name="chemicalfunctions[cfid]" value="{$function->cfid}" />
        <input type="hidden" name="chemicalfunctions[name]" value="{$function->name}" />
        <table cellpadding='0' cellspacing='0' width='100%'>
            <tr style="display: block">
                <td><strong>{$lang->name}</strong></td><td><input name="chemicalfunctions[title]" type="text" value="{$function->title}"/></td>
            </tr>
            <script>
                $(function () {
                    $("input:checkbox[id$='_checkall']").click(function () {
                        var id = $(this).attr('id').split("_");
                        $('input:checkbox[id^="' + id[0] + '"]:visible').not(this).prop('checked', this.checked);
                    });
                });
            </script>
            <tr><td><div style="height: 30px"></div></td><tr>
            <tr style="display:block">
                <td  width="75%">
                    <div style="width:100%; height:150px; overflow:auto; display:inline-block; vertical-align:top; margin-bottom: 10px;">
                        <table class="datatable" width="100%">
                            <thead>
                                <tr>
                                    <th>{$lang->applications}</th>
                                    <th>{$lang->publishonweb}</th>
                                </tr>
                                <tr>
                                    <th width="50%"><input class='inlinefilterfield' type='text' tabindex="2"  placeholder="{$lang->search} {$lang->applications}" style="display:inline-block;width:70%;margin-left:5px;"/></th>
                                    <th><input type="checkbox" id='checkonweb_checkall'></th>
                                </tr>
                            </thead>
                            <tbody>
                                {$applications_list}
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2"><strong>{$lang->description}</strong><br /><textarea rows="5" cols="30" id='description{$function->cfid}' class="basictxteditadv" name="chemicalfunctions[description]">{$function->description}</textarea></td>
            </tr>
            <tr style="display: inline-block">
                <td colspan="2" align="left">
                    <hr />
                    <input type='button' id='perform_products/functions_Button' value='{$lang->savecaps}' class='button'/>
                    <div id="perform_products/functions_Results"></div>
                </td>
            </tr>
    </form>
</div>