<html>
    <body>
        <h1>{$lang->newhoteladded}</h1>
        <table style="border:1px solid #ddd;width:75%">
            <tr style="background-color: #D9D9C2">
                <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->name}</td>
                <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->address}</td>
                <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->city}</td>
                <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->country}</td>
                <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->phone}</td>
                <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->averagepriceinusd}</td>
                <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:15%">{$lang->createdby}</td>
            </tr>
            <tr>
                <td style="border:1px solid #ddd;padding:5px;">{$hotel[name]}</td>
                <td style="border:1px solid #ddd;padding:5px;">{$hotel[addressLine1]}, {$hotel[addressLine2]}</td>
                <td style="border:1px solid #ddd;padding:5px;">{$hotel[city]}</td>
                <td style="border:1px solid #ddd;padding:5px;">{$hotel[country]}</td>
                <td style="border:1px solid #ddd;padding:5px;">{$hotel[phone]}</td>
                <td style="border:1px solid #ddd;padding:5px;">{$hotel[avgPrice]}</td>
                <td style="border:1px solid #ddd;padding:5px;">{$hotel[createdby]}</td>
            </tr>
        </table>
        <br/><br/>
        <table style="border:1px solid #ddd;width:75%">
            <tr style="background-color: #D9D9C2;">
                <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->fax}</td>
                <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->contactperson}</td>
                <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->contactemail}</td>
                <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->website}</td>
                <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->negotiatedcontract}</td>
                <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->distancefromoffice}</td>

            </tr>
            <tr>
                <td style="border:1px solid #ddd;padding:5px;">{$hotel[fax]}</td>
                <td style="border:1px solid #ddd;padding:5px;">{$hotel[contactPerson]}</td>
                <td style="border:1px solid #ddd;padding:5px;">{$hotel[contactEmail]}</td>
                <td style="border:1px solid #ddd;padding:5px;">{$hotel[website]}</td>
                <td style="border:1px solid #ddd;padding:5px;">{$hotel[iscontracted]}</td>
                <td style="border:1px solid #ddd;padding:5px;">{$hotel[distance]}</td>
            </tr>
            <!--  <tr>
                  <td>
                      <a style="font: bold 11px Arial;
                         text-decoration: none;
                         background-color: #EEEEEE;
                         color: #333333;
                         padding: 2px 6px 2px 6px;
                         border-top: 1px solid #CCCCCC;
                         border-right: 1px solid #333333;
                         border-bottom: 1px solid #333333;
                         border-left: 1px solid #CCCCCC;" href="{$newhotel->get_editlink()}&referrer=approve"><button>{$lang->approve}</button></a></td>
              </tr>-->
        </table><br/>
        Click <a href=href="{$newhotel->get_editlink()}&referrer=approve">here</a> To approve the hotel or discuss it;

        <hr>
        <h3>{$lang->hotelsinsamecountry}</h3>
        <table style="border:1px solid #ddd;width:75%">
            <thead {$display[hotelsinsamecountry]}>
                <tr style="background-color: #D9D9C2">
                    <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->name}</td>
                    <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->address}</td>
                    <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->city}</td>
                    <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->country}</td>
                    <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->phone}</td>
                    <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->averagepriceinusd}</td>
                    <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:10%">{$lang->createdby}</td>
                    <td style="font-weight: bold;border:1px solid #ddd;text-align: center;width:5%">{$lang->isapproved}</td>
                </tr>
            </thead>
            <tbody>
                {$hotelsinsamecountrysection}
            </tbody>
        </table>
    </body>
</html>