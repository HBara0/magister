<tr>
    <td style="width:20%;">
        <strong>{$lang->when} </strong><br/>
        <span>{$actions_data[date_otput]}</span>
    </td>
    <td style="width:20%;">
        <span class="subtitle">{$istask}</span>
    </td>
    <td style="width:30%;" rowspan="2">
        <strong>{$lang->what}</strong><br/>
        <span>{$actions_data[what]}</span>
    </td>
</tr>
<tr style="width:100%;">
    <td style="vertical-align: top;width:20%;">
        <div style="display: inline-block;width:100%">
            <table border="0" cellspacing="1" cellpadding="1" width="100%">
                <thead>
                    <tr>
                        <td class="thead" width="100%">{$lang->employee}</td>
                    </tr>
                </thead>
                <tbody>
                    {$actions_users}
                </tbody>
            </table>
        </div>
    </td>
    <td style="vertical-align: top;width:20%;">
        <div style="display: inline-block;width:100%">
            <table border="0" cellspacing="1" cellpadding="1" width="100%">
                <thead>
                    <tr>
                        <td class="thead">{$lang->representative} </td>
                    </tr>
                </thead>
                <tbody>
                    {$actions_representatives}
                </tbody>
            </table>
        </div>
    </td>
</tr>
