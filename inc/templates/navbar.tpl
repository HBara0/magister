<nav class="navbar navbar-default navbar-fixed-top hidden-print" id="main_navbar">
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
                    <a href="#" class="dropdown-toggle scrollable-menu " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="switch_modules"><span class="glyphicon glyphicon-th-large"></span> Main Menu <span class="caret"></span></a>
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
                    <span  style=" font-size:20px; margin-top:15px;" class="glyphicon glyphicon-star-empty" id="frequentlyused_icons"></span>
                </li>


                <li class="hidden-xs">        <div style="position: relative"><img src="{$core->settings[rootdir]}/images/icons/plane-icon1.png" class="img-responsive" alt="Generic placeholder thumbnail" style="margin: 10px;" height="30" width="30">

                    </div></li>
                <li class="hidden-xs">        <div style="position: relative"><img src="{$core->settings[rootdir]}/images/icons/reports-icon.png" class="img-responsive" alt="Generic placeholder thumbnail" style="margin: 10px;" height="30" width="30">

                    </div></li>
                <li class="hidden-xs" style="margin-right:50px;">        <div style="position: relative"><img src="{$core->settings[rootdir]}/images/icons/crm-icon.png" class="img-responsive" alt="Generic placeholder thumbnail" style="margin: 10px;" height="30" width="30">

                    </div></li>
                <li id='updates'><a href="../navbar-static-top/"><span class="glyphicon glyphicon-alert"></span> Updates <span class="badge">42</span></a></li>
                <li class="dropdown" id="userprofile_menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> {$core->user[displayName]}<span class="sr-only">(current)</span> <span class="caret"></span></a>
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