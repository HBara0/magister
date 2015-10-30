<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<link rel="shortcut icon" href="{$core->settings[rootdir]}/images/favicon.ico" />
<script src="{$core->settings[rootdir]}/js/jquery-current.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
<script src="{$core->settings[rootdir]}/js/jquery-ui-current.custom.min.js" type="text/javascript"></script>
<script src="{$core->settings[rootdir]}/js/jquery.cookie.min.js" type="text/javascript"></script>
<script src="{$core->settings[rootdir]}/js/jquery.qtip.min.js" type="text/javascript"></script>
<script src="{$core->settings[rootdir]}/js/jscript.js" type="text/javascript"></script>
<link href="{$core->settings[rootdir]}/css/styles.css" rel="stylesheet" type="text/css" />
<link href='{$core->settings[rootdir]}/css/jqueryuitheme/jquery-ui-current.custom.min.css' rel='stylesheet' type='text/css' />
<link href='{$core->settings[rootdir]}/css/jquery.qtip.min.css' rel='stylesheet' type='text/css' />
<script src="{$core->settings[rootdir]}/js/ckeditor/ckeditor.js"></script>
<script src="{$core->settings[rootdir]}/js/jquery.populate.js" type="text/javascript"></script>

<script type="text/javascript">
    var loading_text = "{$lang->loading}";
    var imagespath = "{$core->settings[rootdir]}/images/";
    var rootdir = "{$core->settings[rootdir]}/";
    window.CKEDITOR_BASEPATH = '{$core->settings[rootdir]}/js/ckeditor';
    var cookie_prefix = "{$core->settings[cookie_prefix]}";
</script>{$additional_inc}