<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->employeeslist}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3>{$meeting[title]}</h3>
            <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px;"> 

                <table  cellspacing="2" cellpadding="2">
                    <tbody>
                        <tr>
                            <td>
                                <strong> {$lang->desc}:</strong>
                            </td>

                            <td>
                                {$meeting[description]}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                {$lang->fromdate}
                            </td>

                            <td>
                                {$meeting[fromDate_output]}: {$meeting[fromTime_output]}  
                            </td>

                        </tr>
                        <tr><td> {$lang->todate}</td><td> {$meeting[toDate_output]}:{$meeting[toTime_output]}</td></tr>

                        <tr>
                            <td>
                                {$lang->location}
                            </td>

                            <td>
                                {$meeting[Location]}
                            </td>


                        </tr>

                        <tr>
                            <td> 
                                <span class="smalltext">  {$lang->createdby}</span> 
                            </td>

                            <td>
                                <span class="smalltext"> {$meeting[createdby]}</span> 
                            </td>

                        </tr>

                    </tbody>
                </table>
            </div>
            {$meetings_viewmeeting_mom}




        </td>
    </tr>
</body>
</html>