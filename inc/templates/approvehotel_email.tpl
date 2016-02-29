<html>
    <body>
        <table>
            <tr><td colspan='2'>
                    <h1 style="color:green">{$lang->newhoteladded} : {$hotel[name]}</h1></td>
            </tr>
            <tr>
                <td colspan='2'><h2>{$hotel[city]}/ {$hotel[country]}</h2></td>
            </tr>
            <tr>
                <td>{$lang->address}</td><td>{$hotel[addressLine1]}, {$hotel[addressLine2]}</td>
            </tr>
            <tr>
                <td>{$lang->phone}</td><td>{$hotel[phone]}</td>
            </tr>
            <tr>
                <td>{$lang->fax}</td><td>{$hotel[fax]}</td>
            </tr>
            <tr>
                <td>{$lang->averagepriceinusd}</td><td>{$hotel[avgPrice]}</td>
            </tr>
            <tr>
                <td>{$lang->negotiatedcontract}</td><td>{$hotel[iscontracted]}</td>
            </tr>
            <tr>
                <td>{$lang->contactperson}</td><td>{$hotel[contactPerson]}</td>
            </tr>
            <tr>
                <td>{$lang->contactemail}</td><td>{$hotel[contactEmail]}</td>
            </tr>
            <tr>
                <td>{$lang->website}</td><td>{$hotel[website]}</td>
            </tr>
            <tr>
                <td>{$lang->distancefromoffice}</td><td>{$hotel[distance]}</td>
            </tr>
            <tr>
                <td>{$lang->createdby}</td><td>{$createdby}</td>
            </tr>
            <tr>
                <td><a style="font: bold 11px Arial;
                       text-decoration: none;
                       background-color: #EEEEEE;
                       color: #333333;
                       padding: 2px 6px 2px 6px;
                       border-top: 1px solid #CCCCCC;
                       border-right: 1px solid #333333;
                       border-bottom: 1px solid #333333;
                       border-left: 1px solid #CCCCCC;" href="{$newhotel->get_editlink()}&referrer=approve"><button>{$lang->approve}</button></a></td>
            </tr>
        </table>
        <hr>
        <div><h3>{$lang->hotelsinsamecountry}</h3></div>
        <table style="width:75%;">
            <thead>
            <th style="width:30%;text-align: center">{$lang->name}</th>
            <th style="width:15%;text-align: center">{$lang->city} </th>
            <th style="width:10%;text-align: center">{$lang->isapproved} </th>
            <th style="width:15%;text-align: center">{$lang->averagepriceinusd}</th>
        </thead>
        <tbody>
            <tr><th colspan="4" style="text-align: center;background-color: #D9D9C2">{$lang->createdhotel}</th></tr>
                    {$newlycreatedhotel_tow}
                    {$hotelsinsamecountrysection}
        </tbody>
    </table>
</body>
</html>