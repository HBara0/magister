<html> 
<head>
<title>{$core->settings[systemtitle]} | {$lang->pleaselogin}</title>
{$headerinc}
</head>
<body>
    <div align="center">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" id="loginbox">
        	<tr>
                <td class="content" align="center" id="resetpasswordcontent" style="display:none;">
                 <h3>{$lang->resetpassword}</h3>       
                    <form id='resetpassword_Form' name='resetpassword_Form' action="#">
                    {$lang->emailtosendpassword}
                    <p><strong>{$lang->email}</strong> <input type='text' id='email' name='email' /> <span id='email_Validation'></span></p>
                    <input type='button' id='resetpassword_Button' value='{$lang->reset}' />
                    <input type="hidden" value="{$token}" name="token" id="resetpasswordtoken" />
                    </form>
                    <div id='resetpassword_Results'></div>
                </td>
            </tr>
            <tr>
                <td class="content" align="center" id="logincontent">
                 <h3>{$lang->pleaselogin}</h3>       
                    <form id='login_Form' name='login_Form' action='#' method="post">
                    <p><strong>{$lang->username}</strong><br /><input type='text' id='username' name='username' /></p>
                    <p><strong>{$lang->password}</strong><br /><input type="password" id="password" name="password" /></p>
                    <input type="hidden" value="{$lastpage}" name="referer" id="referer" />
                    <input type="hidden" value="{$token}" name="token" id="logintoken" />
                    <input type='button' id='login_Button' value='{$lang->login}' />&nbsp;<input type='reset' value='{$lang->reset}' />
                    </form>
                    <div id='login_Results'></div>
                </td>
            </tr>
            <tr><td class="footer"><a href="#" id="resetpassword">{$lang->forgotyourpassword}</a></td></tr>
        </table>
    </div>
</body>
</html>