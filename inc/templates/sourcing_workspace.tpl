<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->requestleave}</title>
        {$headerinc}

        <style type="text/css">
            .kpibelow{
                color:red;
                font-size:50px;
            }
            .kpiabove{
                color:#91b64f;
                font-size:25px; 
                margin-left:30px!important;
            }
            .kiptitle{
                font-size:14px;
                font-weight:bold;
            }
            .kpiperiod{
             font-weight:lighter;
             font-size:13px;
             font-family:cursive;
           
            }

        </style>
    </head>

    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <h3> Workspace</h3> 

            <div class="portalbox" style="width:50%; height:50%; border: 1px solid #000;">

                <div class="kiptitle">{$kpidata[name]}</div>
                <div style="display: block;margin-left:23px;">
                    <div style="display:inline-block; padding:15px;" class="kpiperiod">Current Period </div>
                    <div style="display:inline-block;" class="{$kpibelowtarget}">{$kpipercentage[current]} {$trend_output}</div>
                </div>

                <div style="display: block; width:50%;margin-left:23px;">
                    <div style="display:inline-block;padding:15px;" class="kpiperiod">Previous Month </div>
                    <div style="display:inline-block;" class="{$kpiabovetarget}" >{$kpipercentage[last]}</div>
                </div>
            </div>
        </body>
</html>