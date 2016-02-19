<div style="position:fixed;height:100%;top: 50px; right: 0px;z-index:1;" class="hidden-print">
    <div class="mini-submenu" style=" width: 30px;display: inline-block">
        <span class="glyphicon glyphicon-chevron-left" style="color:#E9E9E9;padding:10px;"  id="switch_pages" data-toggle="tooltip" data-placement="bottom" title="Switch to another Page"></span>
    </div>
    <div class="list-group" style="display: inline-block;margin-left:-5px;">
        <span  class="list-group-item active">
            Facility Management
        </span>
        <a href="index.php?module=facilitymgmt/managefacility" class="list-group-item">
            <i class="fa fa-comment-o"></i> Manage Facility
        </a>
        <a href="index.php?module=facilitymgmt/list" class="list-group-item">
            <i class="fa fa-search"></i> Facilities List
        </a>
        <a href="index.php?module=facilitymgmt/managefacilitytype" class="list-group-item">
            <i class="fa fa-user"></i> Manage Facility Type
        </a>
        <a href="index.php?module=facilitymgmt/typeslist" class="list-group-item">
            <i class="fa fa-folder-open-o"></i> Facility Type List
        </a>
        <a href="index.php?module=facilitymgmt/facilitiesschedule" class="list-group-item">
            <i class="fa fa-bar-chart-o"></i> Facilities Schedule
        </a>
    </div>
</div>
<script>
    $(function() {
        $('#switch_pages').tooltip();
        $('.list-group').toggle();
        $('.mini-submenu').on('click', function() {
            $(this).next('.list-group').animate({width: 'toggle'}, 350);
            if($("span[id='switch_pages']").hasClass("glyphicon-chevron-left")) {
                $("span[id='switch_pages']").removeClass("glyphicon-chevron-left").addClass("glyphicon-chevron-right");
            } else {
                $("span[id='switch_pages']").removeClass("glyphicon-chevron-right").addClass("glyphicon-chevron-left");
            }
        })
    });
</script>