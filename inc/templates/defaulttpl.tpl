<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$pagetitle}</title>
        {$headerinc}
        {$additionalheaderinc}
    </head>
    <body>
        {$header}
        {$rightsidemenu}
        <div class="container workspace_container" id="workspace" style="padding-left:50px;">
            {$pagecontent}
        </div>
        {$footer}

        {$newlayout_helptour}
    </body>
</html>