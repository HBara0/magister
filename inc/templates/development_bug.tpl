<h1>{$bug->summary}</h1>
<table class="datatable">
    <tr><td style="width: 10%;">Severity</td><td>{$bug->severity}</td>
        <td style="width: 10%;">Priority</td><td>{$bug->priority}</td></tr>
    <tr><td>Status</td><td>{$bug->status}</td>
        <td>Fixed</td><td>{$bug->isFixed_output}</td></tr>
    <tr><td>Affected Version</td><td>{$bug->affectedVersion}</td>
        <td>Fixed Version</td><td>{$bug->fixedVersion}</td></tr>
    <tr><td>File</td><td>{$bug->file}</td>
        <td>Line</td><td>{$bug->line}</td></tr>
    <tr><td>Module</td><td>{$bug->module}</td>
        <td>Module File</td><td>{$bug->moduleFile}</td></tr>
    <tr><td>Related Requirement</td><td>{$bug->relatedRequirement_output}</td>
        <td>Reported By</td><td>{$bug->reportBy_output} on {$bug->reportedOn_output} </td></tr>
    <tr><td>Description</td><td colspan="3">{$bug->description_output}</td></tr>
    <tr><td colspan="4"><strong>Stack Trace</strong><br />{$bug->stackTrace_output}</td></tr>
            {$resolutionrow}
</table>
{$resolutionform}