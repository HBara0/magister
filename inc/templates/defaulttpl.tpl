<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$page[title]}</title>

        <!-- Header inc -->
        <meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
        <meta http-equiv="Content-Script-Type" content="text/javascript" />
        <link rel="shortcut icon" href="{$core->settings[rootdir]}/images/favicon.ico" />
        <script src="{$core->settings[rootdir]}/js/jquery-current.min.js" type="text/javascript"></script>
        <script src="{$core->settings[rootdir]}/js/jquery-ui-current.custom.min.js" type="text/javascript"></script>
        <script src="{$core->settings[rootdir]}/js/bootstrap.min.js"></script>
        <script src="{$core->settings[rootdir]}/js/jquery.cookie.min.js" type="text/javascript"></script>
        <script src="{$core->settings[rootdir]}/js/jquery.qtip.min.js" type="text/javascript"></script>
        <script src="{$core->settings[rootdir]}/js/ckeditor/ckeditor.js"></script>
        <script src="{$core->settings[rootdir]}/js/jscript.min.js" type="text/javascript"></script>
        <script src="{$core->settings[rootdir]}/js/jquery.tokeninput.js" type = "text/javascript" ></script>

        <link href='{$core->settings[rootdir]}/css/jqueryuitheme/jquery-ui-current.custom.min.css' rel='stylesheet' type='text/css' />
        <link href="{$core->settings[rootdir]}/css/bootstrap.min.css" rel="stylesheet">
        <link href='{$core->settings[rootdir]}/css/jquery.qtip.min.css' rel='stylesheet' type='text/css' />
        <link href="{$core->settings[rootdir]}/css/token-input.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">

        <link href="{$core->settings[rootdir]}/css/styles.min.css" rel="stylesheet" type="text/css" />
        <link href="{$core->settings[rootdir]}/css/navigationstyle.min.css" rel="stylesheet">

        <script src="{$core->settings[rootdir]}/Datatables/datatables.min.js" type = "text/javascript" ></script>
        <link href='{$core->settings[rootdir]}/Datatables/datatables.min.css' rel='stylesheet' type='text/css' />
        <link href='{$core->settings[rootdir]}/css/dataTables.bootstrap.min.css' rel='stylesheet' type='text/css' />

        <script type="text/javascript">
            var loading_text = "{$lang->loading}";
            var imagespath = "{$core->settings[rootdir]}/images/";
            var rootdir = "{$core->settings[rootdir]}/";
            window.CKEDITOR_BASEPATH = '{$core->settings[rootdir]}/js/ckeditor';
            var cookie_prefix = "{$core->settings[cookie_prefix]}";
        </script>{$additional_inc}

    </head>
    <body>
        <nav class="navbar navbar-default navbar-fixed-top hidden-print">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">OCOS Logo</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="#" style='font-size:18px;'>Home</a></li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle scrollable-menu " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-th-large"></span> Main Menu <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-header">Frequently Used</li>
                                <li><a href="#">Attendance</a></li>
                                <li><a href="#">Budgets</a></li>
                                <li><a href="#">Reports</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="dropdown-header">Others</li>
                                <li><a href="#">ARO</a></li>
                                <li><a href="#">CRM</a></li>
                                <li><a href="#">Facility Management</a></li>
                                <li><a href="#">Warehouse Management</a></li>

                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li id="tooltip" data-toggle="tooltip" data-placement="left" title="Frequently Used" class="hidden-xs">
                            <span  style=" font-size:20px; margin-top:15px;" class="glyphicon glyphicon-star-empty" ></span>
                        </li>


                        <li class="hidden-xs">        <div style="position: relative"><img src="{$core->settings[rootdir]}/images/icons/plane-icon1.png" class="img-responsive" alt="Generic placeholder thumbnail" style="margin: 10px;" height="30" width="30">

                            </div></li>
                        <li class="hidden-xs">        <div style="position: relative"><img src="{$core->settings[rootdir]}/images/icons/reports-icon.png" class="img-responsive" alt="Generic placeholder thumbnail" style="margin: 10px;" height="30" width="30">

                            </div></li>
                        <li class="hidden-xs" style="margin-right:50px;">        <div style="position: relative"><img src="{$core->settings[rootdir]}/images/icons/crm-icon.png" class="img-responsive" alt="Generic placeholder thumbnail" style="margin: 10px;" height="30" width="30">

                            </div></li>
                        <li><a href="../navbar-static-top/"><span class="glyphicon glyphicon-alert"></span> Updates <span class="badge">42</span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> Zaher Reda <span class="sr-only">(current)</span> <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li ><a href="#">Edit Profile</a></li>
                                <li><a href="#">Admin CP</a></li>
                                <li  class="divider" role="separator"></li>
                                <li><a href="#">Log Out</a></li>

                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
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

        <div class="container" style='padding-top:70px;'>
            {$page[content]}
        </div>

        <div class="panel-footer" style="margin-top:10px;">
            <p class="text-muted"> {$lang->copyright} | <a href="mailto:{$core->settings[adminemail]}">{$lang->contactadministrator}</a></p>
        </div>
    </body>

</html>