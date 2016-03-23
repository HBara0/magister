<div style="position:fixed;height:100%;top: 50px;z-index:1;" class="hidden-print">
    <div id="leftside_menu" class="list-group" style="display: inline-block;width:200px;">
        <span  class="list-group-item active">
            <strong>{$lang->$run_module}</strong>
        </span>
        <ul id="mainmenu" style="padding-left:0px !important">
            {$menu_items}
        </ul>
    </div>
    <div class="mini-submenu" style=" width: 30px;display: inline-block;margin-left: -5px;">
        <span class="glyphicon glyphicon-chevron-right" style="color:#E9E9E9;padding:10px;"  id="switch_pages" data-toggle="tooltip" data-placement="bottom" title="Switch to another Page"></span>
    </div>
</div>
<script>
    $(function() {
        $('#switch_pages').tooltip();
        $('#leftside_menu').hide();
        $('.mini-submenu').on('click', function() {
            $(this).prev('.list-group').animate({width: 'toggle'}, 350);
            if($("span[id='switch_pages']").hasClass("glyphicon-chevron-right")) {
                $("span[id='switch_pages']").removeClass("glyphicon-chevron-right").addClass("glyphicon-chevron-left");
            } else {
                $("span[id='switch_pages']").removeClass("glyphicon-chevron-left").addClass("glyphicon-chevron-right");
            }
        })
    });
</script>