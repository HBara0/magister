<div style="position:fixed;height:100%;top: 50px; right: 0px;" class="hidden-print">
    <div class="mini-submenu" style=" width: 30px;display: inline-block">
        <span class="glyphicon glyphicon-chevron-left" style="color:#E9E9E9;padding:10px;"  id="tooltip2" data-toggle="tooltip" data-placement="bottom" title="Switch to another Page"></span>
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
            <i class="fa fa-bar-chart-o"></i> Facilities Schedule  <span class="badge">14</span>
        </a>
    </div>
</div>
<script>
    $(function() {
        $('#tooltip2').tooltip();
        $('.list-group').toggle();
        $('.mini-submenu').on('click', function() {
            $(this).next('.list-group').animate({width: 'toggle'}, 350);
            if($("span[id='tooltip2']").hasClass("glyphicon-chevron-left")) {
                $("span[id='tooltip2']").removeClass("glyphicon-chevron-left").addClass("glyphicon-chevron-right");
            } else {
                $("span[id='tooltip2']").removeClass("glyphicon-chevron-right").addClass("glyphicon-chevron-left");
            }
        })
    });
</script>