<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->pleaselogin}</title>
        {$headerinc}
    </head>
    <body>
        <div align="center">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" id="loginbox">
                <tr>
                    <td class="content" align="center" id="resetpasswordcontent" style="display:none; position: relative;">
                        <h1>{$lang->resetpassword}</h1>
                        <form id='resetpassword_Form' name='resetpassword_Form' action="#">
                            {$lang->emailtosendpassword}
                            <p><strong>{$lang->email}</strong> <input type='text' id='email' name='email' /> <span id='email_Validation'></span></p>
                            <input type='button' id='resetpassword_Button' value='{$lang->reset}' />
                            <input type="hidden" value="{$token}" name="token" id="resetpasswordtoken" />
                        </form>
                        <div id='resetpassword_Results'></div>
                        <div style='position: absolute; top:1px; right: 1px;'>{$helplink}</div>
                    </td>
                </tr>
                <tr>
                    <td class="content" align="center" id="logincontent">
                        <h1>{$lang->pleaselogin}</h1>
                        <div style="width: 50%;">
                            <form id='login_Form' name='login_Form' action='#' method="post">
                                <div style='margin-bottom: 10px;'><input type='text' id='username' name='username' placeholder="{$lang->username}" style="width: 100%; padding: 5px 2px;"/></div>
                                <div style='margin-bottom: 10px;'><input type="password" id="password" name="password" placeholder="{$lang->password}" style="width: 100%; padding: 5px 2px;"/></div>
                                <div style='margin-bottom: 10px;'>
                                    <input type='button' id='login_Button' value='{$lang->login}' style="width: 102%; font-weight: 400; font-size: 14px;" role="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"/>
                                </div>
                                <input type="hidden" value="{$lastpage}" name="referer" id="referer" />
                                <input type="hidden" value="{$token}" name="token" id="logintoken" />
                            </form>
                            <div id='login_Results' class="ui-state-highlight ui-corner-all" style="padding: 2px; display: none;"></div>
                        </div>
                    </td>
                </tr>
                <tr><td class="footer"><a href="#" id="resetpassword">{$lang->forgotyourpassword}</a></td></tr>
            </table>
        </div>
    </body>
</html>