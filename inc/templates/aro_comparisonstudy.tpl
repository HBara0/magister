<a class="header" href="#"><h2 id="aro_currentstock">{$lang->comparisonstudy}</h2></a>
<div>
    <p>
    <table>
        <thead>
            <tr style="vertical-align: top;{$bold}">
                <td class="border_right" rowspan="2" valign="top" align="center" style="width:150px;">-</td>
                <td class="border_right" rowspan="2" valign="top" align="center" style="width:150px;">{$lang->product}</td>
                <td class="border_right" rowspan="2" valign="top" align="center" style="width:100px;">{$lang->lastorder}</td>
                <td class="border_right" rowspan="2" valign="top" align="center" style="width:100px;">{$lang->avglast5orders}</td>
                <td class="border_right" rowspan="2" valign="top" align="center" style="width:100px;">{$lang->avglast10orders}</td>
        </thead>

        <tbody style="width:100%;" class="dataTable">
            {$output}
        </tbody>
    </table>
</p>
</div>