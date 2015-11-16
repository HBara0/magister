<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->topmanagementreport}</title>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">

            google.load("visualization", "1", {packages:["geochart"]});
            google.setOnLoadCallback(drawMap);
            function drawMap() {
                // Create our data table out of JSON data loaded from server.
                var dataArray = [["Country", 1]];
                jQuery.each($mapdata, function(i, val) {
                    var stateitem = [];
                    stateitem.push(i);
                    stateitem.push(val);
                    dataArray.push(stateitem);
                });
                var table = new google.visualization.DataTable();
                table.addColumn('string', 'Country');
                table.addColumn('number', 'population');
                table.addRows(dataArray)

                var options = {};
                options['dataMode'] = 'regions';

                var container = document.getElementById('regions_div');
                var geomap = new google.visualization.GeoChart(container);
                // Wait for the chart to finish drawing before calling the getImageURI() method.
                google.visualization.events.addListener(geomap, 'ready', function() {
                    setTimeout(function() {
                        container.innerHTML = '<img src="' + geomap.getImageURI() + '">';
                    }, 2000);
                });
                geomap.draw(table, options);
            }

        </script>

    </head>
    <body>
        <h1>{$lang->topmanagementreport}</h1>
        <h2>{$lang->orkilacompanies}</h2>
        {$table[nboforkilacompanies]}
        <h2>{$lang->employeespersegment}</h2>
        {$table[employeespersegment]}
        <br/>
        <div>
            <div style="overflow:hidden;display:inline-block;vertical-align: top;width:75%;padding:-70px;"> <!--height: 500px;  width:500px; margin: 0 auto;-->
                <h2>{$lang->affemployeesmap}</h2>
                <div id="regions_div" style="width: 900px; height: 500px;"></div>
            </div>
            <div style="display: inline-block;width:20%">
                {$table[maplegend]}
            </div>
        </div>
        <h2>{$lang->employeesperaff}</h2>
        {$table[employeespercountry]}

    </body>
    {$footer}
</html>