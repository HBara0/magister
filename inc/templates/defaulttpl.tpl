<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$pagetitle}</title>
        {$headerinc}
        {$additionalheaderinc}
    </head>
    <body>
        {$header}
        {$rightsidemenu}
        <div class="container" id="workspace">
            {$pagecontent}
        </div>
        {$footer}

        {$newlayout_helptour}
    </body>

</html>