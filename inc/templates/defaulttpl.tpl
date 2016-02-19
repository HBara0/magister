<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$page[title]}</title>

        {$headerinc}

    </head>
    <body>
        {$header}
        {$rightsidemenu}
        <div class="container" style='padding-top:70px;'>
            {$page[content]}
        </div>
        {$footer}
    </body>

</html>