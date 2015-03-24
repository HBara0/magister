<div id="popup_applicationdescription" title="{$lang->description}">
    <form action="#" method="post" id="perform_products/functions_Form" name="perform_products/functions_Form">
        <input type="hidden" name="action" value="save_descr" />
        <input type='hidden' name='segfuncapp' value='{$safid}'/>
        <textarea rows="5" cols="60" class="texteditormin" name="segapdescription">{$segapdescriptions}</textarea>
        <hr />
        <input type='button' id='perform_products/functions_Button' value='{$lang->savecaps}' class='button'/>
        <div id="perform_products/functions_Results"></div>
    </form>
</div>
<script>
    $('.texteditormin').redactor({
        air: true,
        airButtons: ['bold', 'italic', 'deleted', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'alignleft', 'aligncenter', 'alignright', 'justify'],
        allowedTags: ["br", "p", "b", "i", "del", "strike", "blockquote", "cite", "small", "ul", "ol", "li", "dl", "dt", "dd", "sup", "sub", "pre", "strong", "em"]
    });
    $('.redactor_air').css('z-index', (parseInt($('.ui-dialog').css('z-index')) + 1));
</script>