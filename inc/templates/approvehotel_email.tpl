<html>
    <body>
        <table>
            <tr><td>
                    <h1>{$lang->newhoteladded} : {$hotel['alias']}</h1></td>
            </tr>
            <tr>
                <td><h2>{$hotel['city']}/ {$hotel['country']}</h2></td>
            </tr>
            <tr>
                <td>{$lang->address}</td><td>{$hotel['addressLine1']}, {$hotel['addressLine2']}</td>
            </tr>
            <tr>
                <td>{$lang->phone}</td><td>{$hotel['phone']}</td>
            </tr>
            <tr>
                <td>{$lang->fax}</td><td>{$hotel['fax']}</td>
            </tr>
            <tr>
                <td>{$lang->averagepriceinusd}</td><td>{$hotel['avgPrice']}</td>
            </tr>
            <tr>
                <td>{$lang->negotiatedcontract}</td><td>{$hotel['iscontracted']}</td>
            </tr>
            <tr>
                <td>{$lang->contactperson}</td><td>{$hotel['contactPerson']}</td>
            </tr>
            <tr>
                <td>{$lang->contactemail}</td><td>{$hotel['contactEmail']}</td>
            </tr>
            <tr>
                <td>{$lang->website}</td><td>{$hotel['website']}</td>
            </tr>
            <tr>
                <td>{$lang->distancefromoffice}</td><td>{$hotel['distance']}</td>
            </tr>
            <tr>
                <td><a href="{$newhotel->get_editlink()}&referrer=approve"><button>{$lang->approve}</button></a></td>
            </tr>
        </table>
        <div><h3>{$lang->hotelsinsamecountry}</h3></div>
        <table>
            <thead>
            <th>{$lang->name}</th>
            <th>{$lang->city} </th>
            <th>{$lang->isapproved} </th>
            <th>{$lang->averagepriceinusd}</th>
        </thead>
        <tbody>
            {$hotelsinsamecountrysection}
        </tbody>
    </table>
</body>
</html>