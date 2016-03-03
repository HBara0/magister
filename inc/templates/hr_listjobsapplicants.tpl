<html>
    <head>
        <title>{$core->settings[systemtitle]} | {$lang->listjobsapplicants}</title>
        {$headerinc}
    </head>
    <body>
        {$header}
    <tr>
        {$menu}
        <td class="contentContainer">
            <div style="width:40%; display:inline-block;"><h1>{$lang->listjobsapplicants}</h1></div>
            <table class="datatable_basic table table-bordered row-border hover order-column" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th ></th>
                        <th >{$lang->applicantfullname}</th>
                        <th >{$lang->email}</th>
                        <th >{$lang->phone}</th>
                        <th >{$lang->submissiondate}</th>
                        <th >{$lang->vacancy}</th>
                        <th >{$lang->vacancy} {$lang->affiliate}</th>
                        <th ></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th ></th>
                        <th >{$lang->applicantfullname}</th>
                        <th >{$lang->email}</th>
                        <th >{$lang->phone}</th>
                        <th >{$lang->submissiondate}</th>
                        <th >{$lang->vacancy}</th>
                        <th >{$lang->vacancy} {$lang->affiliate}</th>
                        <th ></th>
                    </tr>
                </tfoot>
                <tbody>
                    {$hr_listjobsapplicants_rows}
                </tbody>
            </table>
            <div align="center">{$map_view}</div>
        </td>
    </tr>
    {$footer}
</body>
</html>