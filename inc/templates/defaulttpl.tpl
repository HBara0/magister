<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$page[title]}</title>
        {$headerinc}
        {$additionalheaderinc}
    </head>
    <body>
        {$header}
        {$rightsidemenu}
        <div class="container" id="workspace">
            {$page[content]}
        </div>
        {$footer}

        {$newlayout_helptour}
    </body>

</html>