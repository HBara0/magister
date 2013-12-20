<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$segment[title]}</title>
        {$headerinc}
        <style type="text/css">
            span.listitem:hover { border-bottom: #CCCCCC solid thin; }
        </style>

    </head>


    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="2"><h3>{$segment[title]}</h3>
                </tr>
                <tr> 
                    <td valign="top" class="border_right" style="width:45%;">  <div class="subtitle">  {$lang->applications}</div>{$segment_applications}<hr /></td>
                    <!--  <td valign="top" class="border_left" style="width:55%;padding:4px;" >
                          <div class="subtitle">{$lang->functions}</div>
                       
                      </td> -->   
                    <td valign="top" class="border_right" style="width:45%;"> <span class="subtitle">{$lang->productypes}</span>
                        {$endproduct_types}
                        <hr />
                    </td>

                </tr>

                <tr>
                    <td  valign="top" style="padding:4px;"><span class="subtitle">{$lang->coordinator}</span>
                        {$segment_coordinators}
                        <hr />
                    </td>
                    <td  valign="top" class="border_left" style="padding:4px;">
                        <span class="subtitle">{$lang->employees}</span>
                        {$segment_employees}
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td valign="top" style="padding:4px;"><span class="subtitle" >{$lang->suppliers}</span><br />
                        {$segment_suppliers}
                        <hr />
                    </td>
                    <td class="border_left" valign="top" style="padding:4px;"><span class="subtitle" >{$lang->customers}</span> 
                        {$segment_customers}
                        <hr />
                    </td>
                </tr>

            </table>
        </td></tr>
        {$footer}
</body>

</html>