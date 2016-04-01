<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->pleaselogin}</title>
        {$headerinc}
        <style>

            body, html {
                padding-top: 40px;
                padding-bottom: 40px;
                background-color: #f3f3f3;
            }
            h2 {
                color: #666;
            }
            .form-signin {
                max-width: 330px;
                padding: 15px;
                margin: 0 auto;
            }
            .form-signin .form-signin-heading,
            .form-signin .checkbox {
                margin-bottom: 10px;
            }
            .form-signin .checkbox {
                font-weight: normal;
            }
            .form-signin .form-control {
                position: relative;
                height: auto;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
                padding: 10px;
                font-size: 16px;
            }
            .form-signin .form-control:focus {
                z-index: 2;
            }
            .form-signin input[type="email"] {
                margin-bottom: -1px;
                border-bottom-right-radius: 0;
                border-bottom-left-radius: 0;
            }
            .form-signin input[type="password"] {
                margin-bottom: 10px;
                border-top-left-radius: 0;
                border-top-right-radius: 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <form class="form-signin" id="logincontent">
                <h2 class="form-signin-heading">{$lang->pleaselogin}</h2>
                <label for="inputEmail" class="sr-only">{$lang->username}</label>
                <input type="text" id="username" name='username' class="form-control" placeholder="{$lang->username}" required autofocus>
                <label for="inputPassword" class="sr-only">{$lang->password}</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="{$lang->password}" required>
                <input class="btn btn-lg btn-success btn-block" type="button" id='login_Button' value="{$lang->login}"/>
                <input type="hidden" value="{$lastpage}" name="referer" id="referer" />
                <div style="text-align:right;">

                    <a href="#" id="resetpassword">{$lang->forgotyourpassword}</a>

                </div>
                <div id='login_Results' class="alert alert-info" style="display: none;" role="alert"></div>
            </form>

            <form class="form-signin" id="resetpasswordcontent" style="display:none; position: relative;">
                <h2 class="form-signin-heading">{$lang->resetpassword}</h2>
                <label for="inputEmail" class="sr-only">{$lang->emailtosendpassword}</label>
                <input type="text" id="email" name='email' class="form-control" placeholder="{$lang->emailtosendpassword}" required autofocus>

                <input class="btn btn-lg btn-success btn-block" type="button" id='resetpassword_Button' value="{$lang->reset}"/>
                <input type="hidden" value="{$token}" name="token" id="resetpasswordtoken" />
                <div id='resetpassword_Results' class="alert alert-info" style="display: none;"role="alert"></div>
                <div style='position: absolute; top:1px; right: 1px;'>{$helplink}</div>
            </form>

        </div> <!-- /container -->
    </body>
</html>