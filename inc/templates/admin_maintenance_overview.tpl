<h1>{$lang->systemoverview}</h1>
<div class="subtitle">{$lang->systemstatus}</div>
<table class="datatable">
    <tr>
        <td class="altrow">{$lang->dbsize}</td><td>{$dbsize}</td><td class="altrow">{$lang->serverload}</td><td>{$serverload}</td>
    </tr>
    <tr>
        <td class="altrow">{$lang->phpversion}</td><td>{$phpversion}</td><td class="altrow">{$lang->gzipcompression}</td><td>{$gzip_status}</td>
    </tr>
</table>

<hr />
<div class="subtitle">{$lang->chmodstatus}</div>
<table class="datatable">
    <tr>
        <td>{$lang->settingsfile}</td><td>./inc/settings.php</td><td>{$chmod[settings]}</td>
    </tr>
    <tr class="altrow">
        <td>{$lang->exportsdirectory}</td><td>./{$core->settings[exportdirectory]}</td><td>{$chmod[exportsdir]}</td>
    </tr>
    <tr>
        <td>{$lang->chartsdirectory}</td><td>./images/charts</td><td>{$chmod[chartsdir]}</td>
    </tr>
</table>
